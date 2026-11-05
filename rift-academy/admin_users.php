<?php
// admin_users.php - Sospensione, riattivazione ed eliminazione utenti (admin)
require_once 'includes/auth.php';
require_once 'includes/db.php';

require_admin();

// TODO: se POST: csrf_check(), poi UPDATE users SET status = ... oppure DELETE (non permettere all'admin di eliminare se stesso)
// TODO: elencare gli utenti con ruolo e stato

render_page('templates/admin_users.html', [
    'title'       => 'Gestione utenti - Rift Academy',
    'description' => 'Gestione degli utenti registrati.',
], [
    'csrf' => '',
    'users_table' => '',
]);
