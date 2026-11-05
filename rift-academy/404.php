<?php
// 404.php - Pagina di errore personalizzata (pubblica)
require_once 'includes/auth.php';

http_response_code(404);

render_page('templates/404.html', [
    'title'       => 'Pagina non trovata - Rift Academy',
    'description' => 'La pagina richiesta non esiste.',
]);
