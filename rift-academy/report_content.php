<?php
// report_content.php - Segnalazione di una guida o di un commento (solo POST)
require_once 'includes/auth.php';
require_once 'includes/db.php';

require_login();
require_post();
csrf_check();

// TODO: validare guide_id oppure comment_id (uno dei due) e il motivo (testo libero)
// TODO: INSERT in reports con status 'open'
// TODO: flash_set('Segnalazione inviata.'); redirect alla pagina di provenienza

redirect('index.php');
