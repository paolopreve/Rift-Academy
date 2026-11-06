<?php
// champions.php - Catalogo campioni filtrabile per ruolo (pubblica)
require_once 'includes/auth.php';
require_once 'includes/db.php';

// Leggere il filtro dal parametro GET 'role' (validare contro la tabella roles)
$selectedRole = get_int('role');

// Preparare la query per ottenere i ruoli disponibili
$stmt = db()->prepare('SELECT id, name FROM roles');
$stmt->execute();
$roles = $stmt->fetchAll();

// Validare il ruolo selezionato
$roleOptionsHtml = '';
$roleInRoles = false;
foreach ($roles as $role) {
    $roleOptionsHtml .= '<option value="' . e($role['id']) . '"' . ($selectedRole === $role['id'] ? ' selected' : '') . '>' . e($role['name']) . '</option>';
    if ($selectedRole === $role['id']) {
        $roleInRoles = true;
    }
}

// Se il ruolo non è valido, resettarlo a null
if (!$roleInRoles) {
    $selectedRole = null;
}

// Preparare la query per ottenere i campioni filtrati per ruolo
$query = 'SELECT c.id, c.name AS champion_name, r.name AS role_name
          FROM champions c
          JOIN champion_roles cr ON c.id = cr.champion_id
          JOIN roles r ON cr.role_id = r.id';
if ($selectedRole !== null) {
    $query .= ' WHERE cr.role_id = ?';
}
$query .= ' ORDER BY c.name';

$stmt = db()->prepare($query);
if ($selectedRole !== null) {
    $stmt->execute([$selectedRole]);
} else {
    $stmt->execute();
}
$champions = $stmt->fetchAll();

// Costruire l'HTML della lista dei campioni
$championsListHtml = '';
foreach ($champions as $champion) {
    $championsListHtml .= '<div class="champion-card">';
    $championsListHtml .= '<h2>' . e($champion['champion_name']) . '</h2>';
    $championsListHtml .= '<p><strong>Ruolo:</strong> ' . e($champion['role_name']) . '</p>';
    $championsListHtml .= '<a href="champion.php?id=' . e($champion['id']) . '">Dettagli del campione</a>';
    $championsListHtml .= '</div>';
}

render_page('templates/champions.html', [
    'title'       => 'Campioni - Rift Academy',
    'description' => 'Catalogo dei campioni di League of Legends filtrabile per ruolo.',
], [
    'role_options' => $roleOptionsHtml,
    'champions_list' => $championsListHtml,
]);