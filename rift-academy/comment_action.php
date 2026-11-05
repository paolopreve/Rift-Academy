<?php
// comment_action.php - Crea o elimina un commento (autore o admin per l'eliminazione) (solo POST)
require_once 'includes/auth.php';
require_once 'includes/db.php';

require_login();
require_post();
csrf_check();

// TODO: leggere 'action' (create/delete), guide_id, comment_id, body
// TODO: create: validare il testo libero (lunghezza, non vuoto) e INSERT in comments
// TODO: delete: verificare is_owner_or_admin sul commento, poi DELETE
// TODO: redirect a guide_detail.php?id=<guide_id>

redirect('index.php');
