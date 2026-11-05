<?php
// guide_detail.php - Dettaglio guida: build, rune, strategia, commenti, voti (pubblica)
require_once 'includes/auth.php';
require_once 'includes/db.php';

// TODO: leggere id da GET (get_int('id')); se la guida non esiste rispondere con 404.php
// TODO: caricare guida, oggetti, rune, media voti (AVG) e commenti
// TODO: mostrare i pulsanti valuta / preferito / segnala / modifica solo se ha senso per l'utente
// TODO: impostare il titolo della pagina con il titolo della guida

render_page('templates/guide_detail.html', [
    'title'       => 'Guida - Rift Academy',
    'description' => 'Guida e build per League of Legends.',
], [
    'breadcrumb' => '',
    'guide_title' => '',
    'guide_meta' => '',
    'rating' => '',
    'guide_strategy' => '',
    'guide_build' => '',
    'comments' => '',
    'comment_form' => '',
]);
