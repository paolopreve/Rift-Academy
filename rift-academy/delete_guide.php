<?php
// delete_guide.php - Eliminazione di una guida (autore o admin) (solo POST)
require_once 'includes/auth.php';
require_once 'includes/db.php';

require_login();
require_post();
csrf_check();

$guide_id = (int) post_str('guide_id');

$stmt = db()->prepare('SELECT id, user_id FROM guides WHERE id = ?');
$stmt->execute([$guide_id]);
$guide = $stmt->fetch();

if (!$guide) {
    flash_set('Guida non trovata.');
    redirect('profile.php');
}

// Controllo di autorizzazione lato server
if (!is_owner_or_admin((int) $guide['user_id'])) {
    http_response_code(403);
    exit('Accesso negato.');
}

$stmt = db()->prepare('DELETE FROM guides WHERE id = ?');
$stmt->execute([$guide_id]);

flash_set('Guida eliminata.');
redirect('profile.php');
