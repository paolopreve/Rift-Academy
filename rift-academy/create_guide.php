<?php
// create_guide.php - Creazione di una nuova guida (utente registrato)
require_once 'includes/auth.php';
require_once 'includes/db.php';

require_login();

// TODO: caricare campioni, ruoli, oggetti e rune per i campi di selezione
// TODO: se POST: csrf_check(), validare titolo, strategia (testo libero), patch, selezioni
// TODO: INSERT in guides, guide_items, guide_runes (transazione) con prepared statement
// TODO: redirect a guide_detail.php?id=... dopo il salvataggio

render_page('templates/create_guide.html', [
    'title'       => 'Nuova guida - Rift Academy',
    'description' => 'Pubblica una nuova guida o build.',
], [
    'csrf' => '',
    'errors' => '',
    'champion_options' => '',
    'role_options' => '',
    'item_checkboxes' => '',
    'rune_checkboxes' => '',
]);
