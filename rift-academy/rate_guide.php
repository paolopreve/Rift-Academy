<?php
// rate_guide.php - Valutazione di una guida da 1 a 5 (una per utente) (solo POST)
require_once 'includes/auth.php';
require_once 'includes/db.php';

require_login();
require_post();
csrf_check();

// TODO: validare guide_id e valore 1-5; vietare di valutare la propria guida (scelta di progetto)
// TODO: INSERT ... ON DUPLICATE KEY UPDATE su ratings
// TODO: redirect a guide_detail.php?id=<guide_id>

redirect('index.php');
