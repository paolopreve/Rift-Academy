<?php
// search.php - Risultati della ricerca testuale su guide e campioni (pubblica)
require_once 'includes/auth.php';
require_once 'includes/db.php';

// TODO: leggere 'q' da GET, validare, cercare con prepared statement (FULLTEXT oppure LIKE)

render_page('templates/search.html', [
    'title'       => 'Cerca - Rift Academy',
    'description' => 'Risultati della ricerca su guide e campioni.',
], [
    'query' => '',
    'results' => '',
]);
