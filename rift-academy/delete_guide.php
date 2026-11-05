<?php
// delete_guide.php - Eliminazione di una guida (autore o admin) (solo POST)
require_once 'includes/auth.php';
require_once 'includes/db.php';

require_login();
require_post();
csrf_check();

// TODO: leggere guide_id, caricare la guida e verificare is_owner_or_admin((int) $guide['user_id'])
// TODO: DELETE FROM guides WHERE id = ? (prepared statement)
// TODO: flash_set('Guida eliminata.'); redirect('profile.php');

redirect('profile.php');
