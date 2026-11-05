<?php
// edit_guide.php - Modifica di una guida esistente (autore o admin)
require_once 'includes/auth.php';
require_once 'includes/db.php';

require_login();

// TODO: leggere la guida da GET 'id'; se is_owner_or_admin((int) $guide['user_id']) e' falso rispondere 403
// TODO: se POST: csrf_check(), validare, UPDATE ... SET ..., updated_at = NOW() WHERE id = ?

render_page('templates/edit_guide.html', [
    'title'       => 'Modifica guida - Rift Academy',
    'description' => 'Modifica una tua guida.',
], [
    'csrf' => '',
    'errors' => '',
    'guide_id' => '',
    'champion_options' => '',
    'role_options' => '',
    'item_checkboxes' => '',
    'rune_checkboxes' => '',
]);
