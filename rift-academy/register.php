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
    $email    = post_str('email');
    $password = isset($_POST['password']) && is_string($_POST['password']) ? $_POST['password'] : '';

    // TODO: validazione lato server (lunghezze, formato email con filter_var, password minima)
    // TODO: controllare che username ed email non esistano gia'
    // TODO: INSERT con prepared statement e password_hash($password, PASSWORD_DEFAULT)
    // TODO: login_user(...) oppure redirect('login.php') con flash_set(...)
}

render_page('templates/register.html', [
    'title'       => 'Registrati - Rift Academy',
    'description' => 'Crea un account Rift Academy per pubblicare guide e commentare.',
], [
    'csrf'   => csrf_field(),
    'errors' => errors_html($errors),
]);
