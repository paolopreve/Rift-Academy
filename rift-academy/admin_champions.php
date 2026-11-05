<?php
// admin_champions.php - Inserimento e aggiornamento di campioni, oggetti e rune (admin)
require_once 'includes/auth.php';
require_once 'includes/db.php';

require_admin();

// TODO: se POST: csrf_check(), validare e INSERT/UPDATE (campioni con i loro ruoli in champion_roles)
// TODO: i ruoli di corsia (Top, Jungle, Mid, Bot, Support) NON sono nei dati Data Dragon: si assegnano qui

render_page('templates/admin_champions.html', [
    'title'       => 'Catalogo campioni - Rift Academy',
    'description' => 'Gestione del catalogo di campioni, oggetti e rune.',
], [
    'csrf' => '',
    'errors' => '',
    'role_checkboxes' => '',
    'champions_table' => '',
]);
