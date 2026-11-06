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

    // Validazione lato server dei camp
    if (empty($username) || empty($password)) {
        $errors[] = 'Compilare tutti i campi.';
    }

    if (empty($errors)) {
        // SELECT dell'utente per username con prepared statement
        $stmt = db()->prepare('SELECT id, username, password_hash, status FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $row = $stmt->fetch();

        // Verifica password e stato utente
        if ($row && password_verify($password, $row['password_hash']) && $row['status'] === 'active') {
            login_user($row);
            flash_set('Accesso eseguito.');
            redirect('profile.php');
        } else {
            $errors[] = 'Credenziali non valide.';
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
