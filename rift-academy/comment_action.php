<?php
// comment_action.php - Crea o elimina un commento (autore o admin per l'eliminazione) (solo POST)
require_once 'includes/auth.php';
require_once 'includes/db.php';

require_login();
require_post();
csrf_check();

$action = post_str('action');

if ($action === 'create') {
    $guide_id = (int) post_str('guide_id');
    $body = post_str('body');

    $stmt = db()->prepare('SELECT id FROM guides WHERE id = ?');
    $stmt->execute([$guide_id]);
    if (!$stmt->fetch()) {
        flash_set('Guida non trovata.');
        redirect('index.php');
    }

    if ($body === '' || strlen($body) > 2000) {
        flash_set('Il commento non può essere vuoto né superare 2000 caratteri.');
        redirect('guide_detail.php?id=' . $guide_id);
    }

    $stmt = db()->prepare('INSERT INTO comments (guide_id, user_id, body) VALUES (?, ?, ?)');
    $stmt->execute([$guide_id, current_user()['id'], $body]);

    flash_set('Commento aggiunto.');
    redirect('guide_detail.php?id=' . $guide_id);
}

if ($action === 'delete') {
    $comment_id = (int) post_str('comment_id');

    $stmt = db()->prepare('SELECT id, guide_id, user_id FROM comments WHERE id = ?');
    $stmt->execute([$comment_id]);
    $comment = $stmt->fetch();

    if (!$comment) {
        flash_set('Commento non trovato.');
        redirect('index.php');
    }

    // Solo l'autore del commento o un admin
    if (!is_owner_or_admin((int) $comment['user_id'])) {
        http_response_code(403);
        exit('Accesso negato.');
    }

    $stmt = db()->prepare('DELETE FROM comments WHERE id = ?');
    $stmt->execute([$comment_id]);

    flash_set('Commento eliminato.');
    redirect('guide_detail.php?id=' . (int) $comment['guide_id']);
}

flash_set('Azione non valida.');
redirect('index.php');
