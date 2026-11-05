<?php
// champions.php - Catalogo campioni filtrabile per ruolo (pubblica)
require_once 'includes/auth.php';
require_once 'includes/db.php';

// TODO: leggere il filtro dal parametro GET 'role' (validare contro la tabella roles)
// TODO: SELECT dei campioni (JOIN champion_roles) con prepared statement

render_page('templates/champions.html', [
    'title'       => 'Campioni - Rift Academy',
    'description' => 'Catalogo dei campioni di League of Legends filtrabile per ruolo.',
], [
    'role_options' => '',
    'champions_list' => '',
]);
