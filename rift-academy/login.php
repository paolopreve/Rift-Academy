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

    // TODO: validazione lato server dei campi
    // TODO: SELECT dell'utente per username con prepared statement
    // TODO: password_verify($password, $row['password_hash'])
    // TODO: rifiutare se $row['status'] !== 'active' (utente sospeso)
    // TODO: login_user($row); flash_set('Accesso eseguito.'); redirect('profile.php');
    // Usare un messaggio unico per credenziali errate (non rivelare quale campo e' sbagliato).
}

render_page('templates/login.html', [
    'title'       => 'Accedi - Rift Academy',
    'description' => 'Accedi al tuo account Rift Academy.',
], [
    'csrf'   => csrf_field(),
    'errors' => errors_html($errors),
]);
