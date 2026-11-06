<?php
// comment_action.php - Crea o elimina un commento (autore o admin per l'eliminazione) (solo POST)
require_once 'includes/auth.php';
require_once 'includes/db.php';

require_login();
require_post();
csrf_check();

$action = post_str('action');
$guide_id = get_int('guide_id');
$comment_id = get_int('comment_id');
$body = post_str('body');

if ($action === 'create') {
    if (empty($body)) {
        flash_set('Il commento non pu\u00f2 essere vuoto.');
        redirect("guide_detail.php?id={$guide_id}");
    }

    $stmt = db()->prepare('INSERT INTO comments (user_id, guide_id, body, created_at) VALUES (:user_id, :guide_id, :body, NOW())');
    $stmt->execute([
        ':user_id' => current_user()['id'],
        ':guide_id' => $guide_id,
        ':body' => $body,
    ]);

    flash_set('Commento aggiunto con successo.');
} elseif ($action === 'delete') {
    $stmt = db()->prepare('SELECT user_id FROM comments WHERE id = :comment_id');
    $stmt->execute([':comment_id' => $comment_id]);
    $comment = $stmt->fetch();

    if (!$comment || !is_owner_or_admin($comment['user_id'])) {
        flash_set('Non hai i permessi per eliminare questo commento.');
        redirect('index.php');
    }

    $stmt = db()->prepare('DELETE FROM comments WHERE id = :comment_id');
    $stmt->execute([':comment_id' => $comment_id]);

    flash_set('Commento eliminato con successo.');
} else {
    flash_set('Azione non valida.');
}

redirect("guide_detail.php?id={$guide_id}");
