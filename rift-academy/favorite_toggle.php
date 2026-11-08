<?php
// favorite_toggle.php - Aggiunge o rimuove una guida dai preferiti (solo POST)
require_once 'includes/auth.php';
require_once 'includes/db.php';

require_login();
require_post();
csrf_check();

$guide_id = (int) post_str('guide_id');
$user_id = current_user()['id'];

$stmt = db()->prepare('SELECT id FROM guides WHERE id = ?');
$stmt->execute([$guide_id]);
if (!$stmt->fetch()) {
    flash_set('Guida non trovata.');
    redirect('index.php');
}

// Se il preferito esiste lo elimina, altrimenti lo inserisce
$stmt = db()->prepare('SELECT guide_id FROM favorites WHERE user_id = ? AND guide_id = ?');
$stmt->execute([$user_id, $guide_id]);

if ($stmt->fetch()) {
    $stmt = db()->prepare('DELETE FROM favorites WHERE user_id = ? AND guide_id = ?');
    $stmt->execute([$user_id, $guide_id]);
    flash_set('Guida rimossa dai preferiti.');
} else {
    $stmt = db()->prepare('INSERT INTO favorites (user_id, guide_id) VALUES (?, ?)');
    $stmt->execute([$user_id, $guide_id]);
    flash_set('Guida aggiunta ai preferiti.');
}

redirect('guide_detail.php?id=' . $guide_id);
