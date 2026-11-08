<?php
// rate_guide.php - Valutazione di una guida da 1 a 5 (una per utente) (solo POST)
require_once 'includes/auth.php';
require_once 'includes/db.php';

require_login();
require_post();
csrf_check();

$guide_id = (int) post_str('guide_id');
$value = (int) post_str('value');

$stmt = db()->prepare('SELECT id, user_id FROM guides WHERE id = ?');
$stmt->execute([$guide_id]);
$guide = $stmt->fetch();

if (!$guide) {
    flash_set('Guida non trovata.');
    redirect('index.php');
}

if ($value < 1 || $value > 5) {
    flash_set('Il voto deve essere compreso tra 1 e 5.');
    redirect('guide_detail.php?id=' . $guide_id);
}

// Scelta di progetto: non si può valutare la propria guida
if ((int) $guide['user_id'] === current_user()['id']) {
    flash_set('Non puoi valutare una tua guida.');
    redirect('guide_detail.php?id=' . $guide_id);
}

// Un solo voto per utente: se esiste viene aggiornato
$stmt = db()->prepare('INSERT INTO ratings (guide_id, user_id, value) VALUES (?, ?, ?)
                       ON DUPLICATE KEY UPDATE value = VALUES(value)');
$stmt->execute([$guide_id, current_user()['id'], $value]);

flash_set('Voto registrato.');
redirect('guide_detail.php?id=' . $guide_id);
