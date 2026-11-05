<?php
// logout.php - Chiusura della sessione (solo POST)
require_once 'includes/auth.php';

require_post();
csrf_check();
logout_user();

// La sessione e' stata distrutta: riaprirla per il messaggio.
session_start();
flash_set('Hai effettuato il logout.');
redirect('index.php');
