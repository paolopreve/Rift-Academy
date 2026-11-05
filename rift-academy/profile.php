<?php
// profile.php - Profilo: proprie guide e preferiti (utente registrato)
require_once 'includes/auth.php';
require_once 'includes/db.php';

require_login();

// TODO: elencare le guide dell'utente con link a edit_guide.php e pulsante di eliminazione (POST + csrf)
// TODO: elencare i preferiti (tabella favorites)

render_page('templates/profile.html', [
    'title'       => 'Il tuo profilo - Rift Academy',
    'description' => 'Le tue guide e i tuoi preferiti.',
], [
    'my_guides' => '',
    'favorites' => '',
]);
