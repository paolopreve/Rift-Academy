<?php
// login.php - Accesso utente (pubblica)
require_once 'includes/auth.php';
require_once 'includes/db.php';

if (current_user() !== null) {
    redirect('profile.php');
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $username = post_str('username');
    $password = isset($_POST['password']) && is_string($_POST['password']) ? $_POST['password'] : '';

    // Validazione lato server dei campi
    if ($username === '' || $password === '') {
        $errors[] = 'Compila nome utente e password.';
    }

    if (empty($errors)) {
        // Cerca l'utente per username (serve anche il ruolo per la sessione)
        $stmt = db()->prepare('SELECT id, username, password_hash, role, status FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $row = $stmt->fetch();

        // Messaggio unico per credenziali errate o utente sospeso
        if ($row && password_verify($password, $row['password_hash']) && $row['status'] === 'active') {
            login_user($row);
            flash_set('Accesso eseguito.');
            redirect('profile.php');
        } else {
            $errors[] = 'Nome utente o password non validi, oppure account non attivo.';
        }
    }
}

render_page('templates/login.html', [
    'title'       => 'Accedi - Rift Academy',
    'description' => 'Accedi al tuo account Rift Academy.',
], [
    'csrf'   => csrf_field(),
    'errors' => errors_html($errors),
]);
