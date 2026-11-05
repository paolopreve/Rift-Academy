<?php
// index.php - Home: ultime guide e ricerca rapida (pubblica)
require_once 'includes/auth.php';
require_once 'includes/db.php';

// TODO: leggere le ultime guide (ORDER BY created_at DESC LIMIT 6) con prepared statement
// TODO: costruire l'HTML delle card, usando e() per ogni dato utente

render_page('templates/index.html', [
    'title'       => 'Rift Academy - Guide e build per League of Legends',
    'description' => 'Guide, build e rune per campione e ruolo, scritte dalla community di League of Legends.',
], [
    'latest_guides' => '',
]);
