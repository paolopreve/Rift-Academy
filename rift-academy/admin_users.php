<?php
// admin_users.php - Sospensione, riattivazione ed eliminazione utenti (admin)
require_once 'includes/auth.php';
require_once 'includes/db.php';

require_admin();

$selfId = current_user()['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = post_str('action');
    $targetId = (int) post_str('user_id');

    $stmt = db()->prepare('SELECT id FROM users WHERE id = ?');
    $stmt->execute([$targetId]);

    if (!$stmt->fetch()) {
        flash_set('Utente non trovato.');
    } elseif ($targetId === $selfId) {
        // L'admin non può sospendere né eliminare se stesso
        flash_set('Non puoi sospendere o eliminare il tuo account.');
    } elseif ($action === 'suspend') {
        $stmt = db()->prepare('UPDATE users SET status = ? WHERE id = ?');
        $stmt->execute(['suspended', $targetId]);
        flash_set('Utente sospeso.');
    } elseif ($action === 'activate') {
        $stmt = db()->prepare('UPDATE users SET status = ? WHERE id = ?');
        $stmt->execute(['active', $targetId]);
        flash_set('Utente riattivato.');
    } elseif ($action === 'delete') {
        $stmt = db()->prepare('DELETE FROM users WHERE id = ?');
        $stmt->execute([$targetId]);
        flash_set('Utente eliminato.');
    } else {
        flash_set('Azione non valida.');
    }
    redirect('admin_users.php');
}

$users = db()->query('SELECT id, username, email, role, status FROM users ORDER BY username')->fetchAll();

$table = '<table><caption>Utenti registrati</caption><thead><tr>'
    . '<th scope="col">Utente</th><th scope="col">Email</th><th scope="col">Ruolo</th><th scope="col">Stato</th><th scope="col">Azioni</th>'
    . '</tr></thead><tbody>';
foreach ($users as $u) {
    $uid = e($u['id']);
    $table .= '<tr><th scope="row">' . e($u['username']) . '</th><td>' . e($u['email']) . '</td><td>' . e($u['role']) . '</td><td>'
        . ($u['status'] === 'active' ? 'Attivo' : 'Sospeso') . '</td><td>';
    if ((int) $u['id'] === $selfId) {
        $table .= 'Il tuo account';
    } else {
        $toggle = ($u['status'] === 'active') ? 'suspend' : 'activate';
        $toggleLabel = ($u['status'] === 'active') ? 'Sospendi ' : 'Riattiva ';
        $table .= '<form action="admin_users.php" method="post">' . csrf_field()
            . '<input type="hidden" name="action" value="' . $toggle . '" />'
            . '<input type="hidden" name="user_id" value="' . $uid . '" />'
            . '<button type="submit">' . $toggleLabel . e($u['username']) . '</button></form>'
            . '<form action="admin_users.php" method="post">' . csrf_field()
            . '<input type="hidden" name="action" value="delete" />'
            . '<input type="hidden" name="user_id" value="' . $uid . '" />'
            . '<button type="submit">Elimina ' . e($u['username']) . '</button></form>';
    }
    $table .= '</td></tr>';
}
$table .= '</tbody></table>';

render_page('templates/admin_users.html', [
    'title'       => 'Gestione utenti - Rift Academy',
    'description' => 'Gestione degli utenti registrati.',
], [
    'csrf'        => '',
    'users_table' => $table,
]);
