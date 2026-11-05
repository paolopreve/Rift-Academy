<?php
// admin_dashboard.php - Moderazione di guide, commenti e segnalazioni (admin)
require_once 'includes/auth.php';
require_once 'includes/db.php';

require_admin();

// TODO: se POST: csrf_check(), poi eliminare la guida o il commento, oppure chiudere la segnalazione
// TODO: elencare le segnalazioni aperte con link al contenuto

render_page('templates/admin_dashboard.html', [
    'title'       => 'Moderazione - Rift Academy',
    'description' => 'Pannello di moderazione.',
], [
    'csrf' => '',
    'reports' => '',
    'latest_guides' => '',
]);
