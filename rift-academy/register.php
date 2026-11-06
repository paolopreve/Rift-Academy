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

    // Validazione lato server
    if (strlen($username) < 3 || strlen($username) > 20) {
        $errors[] = 'Il nome utente deve avere tra 3 e 20 caratteri.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Inserisci un indirizzo email valido.';
    }
    if (strlen($password) < 6) {
        $errors[] = 'La password deve avere almeno 6 caratteri.';
    }

    if (empty($errors)) {
        // Controllare che username ed email non esistano già
        $stmt = db()->prepare('SELECT id FROM users WHERE username = ? OR email = ?');
        $stmt->execute([$username, $email]);
        $userExists = $stmt->fetch();

        if ($userExists) {
            $errors[] = 'Username o email già in uso.';
        } else {
            // INSERT con prepared statement e password_hash($password, PASSWORD_DEFAULT)
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = db()->prepare('INSERT INTO users (username, email, password_hash, role, status) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([$username, $email, $hashedPassword, 'user', 'active']);

            $userId = db()->lastInsertId();
            if ($userId) {
                $stmt = db()->prepare('SELECT id, username, password_hash, status FROM users WHERE id = ?');
                $stmt->execute([$userId]);
                $userRow = $stmt->fetch();
                login_user($userRow);
                flash_set('Registrazione completata. Benvenuto su Rift Academy!');
                redirect('profile.php');
            } else {
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
