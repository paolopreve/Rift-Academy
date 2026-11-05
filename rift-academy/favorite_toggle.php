<?php
// favorite_toggle.php - Aggiunge o rimuove una guida dai preferiti (solo POST)
require_once 'includes/auth.php';
require_once 'includes/db.php';

require_login();
require_post();
csrf_check();

// TODO: se la riga esiste in favorites eliminarla, altrimenti inserirla
// TODO: redirect a guide_detail.php?id=<guide_id>

redirect('index.php');
