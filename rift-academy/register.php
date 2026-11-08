<?php
// register.php - Registrazione nuovo utente (pubblica)
require_once 'includes/auth.php';
require_once 'includes/db.php';

if (current_user() !== null) {
    redirect('profile.php');
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $username = post_str('username');
    $email = post_str('email');
    $password = isset($_POST['password']) && is_string($_POST['password']) ? $_POST['password'] : '';

    // Validazione lato server (stessi limiti del template e dello schema)
    if (strlen($username) < 3 || strlen($username) > 30) {
        $errors[] = 'Il nome utente deve avere tra 3 e 30 caratteri.';
    }
    if (strlen($email) > 100 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Inserisci un indirizzo email valido (massimo 100 caratteri).';
    }
    if (strlen($password) < 8) {
        $errors[] = 'La password deve avere almeno 8 caratteri.';
    }

    if (empty($errors)) {
        // Controlla che username ed email non esistano già
        $stmt = db()->prepare('SELECT id FROM users WHERE username = ? OR email = ?');
        $stmt->execute([$username, $email]);

        if ($stmt->fetch()) {
            $errors[] = 'Nome utente o email già in uso: scegline un altro.';
        } else {
            try {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = db()->prepare('INSERT INTO users (username, email, password_hash, role, status) VALUES (?, ?, ?, ?, ?)');
                $stmt->execute([$username, $email, $hash, 'user', 'active']);

                // Login automatico: serve il ruolo per la sessione
                $stmt = db()->prepare('SELECT id, username, role FROM users WHERE id = ?');
                $stmt->execute([(int) db()->lastInsertId()]);
                login_user($stmt->fetch());
                flash_set('Registrazione completata. Benvenuto su Rift Academy!');
                redirect('profile.php');
            } catch (PDOException $ex) {
                error_log($ex->getMessage());
                $errors[] = 'Errore durante la registrazione. Riprova più tardi.';
            }
        }
    }
}

render_page('templates/register.html', [
    'title'       => 'Registrati - Rift Academy',
    'description' => 'Crea un account Rift Academy per pubblicare guide e commentare.',
], [
    'csrf'   => csrf_field(),
    'errors' => errors_html($errors),
]);
