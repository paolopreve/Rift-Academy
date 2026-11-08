<?php
// report_content.php - Segnalazione di una guida o di un commento (solo POST)
require_once 'includes/auth.php';
require_once 'includes/db.php';

require_login();
require_post();
csrf_check();

$guide_id = (int) post_str('guide_id');
$comment_id = (int) post_str('comment_id');
$reason = post_str('reason');

// Deve essere valorizzato esattamente uno tra guide_id e comment_id
if (($guide_id > 0) === ($comment_id > 0)) {
    flash_set('Segnalazione non valida.');
    redirect('index.php');
}

if ($reason === '' || strlen($reason) > 255) {
    flash_set('Indica il motivo della segnalazione (massimo 255 caratteri).');
}

// Pagina di provenienza e verifica che il contenuto esista
$back_guide_id = $guide_id;
if ($comment_id > 0) {
    $stmt = db()->prepare('SELECT guide_id FROM comments WHERE id = ?');
    $stmt->execute([$comment_id]);
    $row = $stmt->fetch();
    if (!$row) {
        flash_set('Commento non trovato.');
        redirect('index.php');
    }
    $back_guide_id = (int) $row['guide_id'];
} else {
    $stmt = db()->prepare('SELECT id FROM guides WHERE id = ?');
    $stmt->execute([$guide_id]);
    if (!$stmt->fetch()) {
        flash_set('Guida non trovata.');
        redirect('index.php');
    }
}

// Motivo non valido: il messaggio è già stato impostato sopra
if ($reason === '' || strlen($reason) > 255) {
    redirect('guide_detail.php?id=' . $back_guide_id);
}

$stmt = db()->prepare('INSERT INTO reports (reporter_id, guide_id, comment_id, reason, status) VALUES (?, ?, ?, ?, ?)');
$stmt->execute([
    current_user()['id'],
    $guide_id > 0 ? $guide_id : null,
    $comment_id > 0 ? $comment_id : null,
    $reason,
    'open',
]);

flash_set('Segnalazione inviata.');
redirect('guide_detail.php?id=' . $back_guide_id);
