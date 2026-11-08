<?php
// champions.php - Catalogo campioni filtrabile per ruolo (pubblica)
require_once 'includes/auth.php';
require_once 'includes/db.php';

// Filtro letto da GET e validato contro la tabella roles
$selectedRole = get_int('role');

$roles = db()->query('SELECT id, name FROM roles ORDER BY id')->fetchAll();

$roleOptionsHtml = '<option value="0">Tutti i ruoli</option>';
$roleValid = false;
foreach ($roles as $role) {
    $isSelected = ((int) $role['id'] === $selectedRole);
    if ($isSelected) {
        $roleValid = true;
    }
    $roleOptionsHtml .= '<option value="' . e($role['id']) . '"' . ($isSelected ? ' selected="selected"' : '') . '>' . e($role['name']) . '</option>';
}
if (!$roleValid) {
    $selectedRole = 0;
}

// Un campione può avere più ruoli: GROUP_CONCAT evita righe duplicate
$query = 'SELECT c.id, c.name, c.title, c.image_path,
                 GROUP_CONCAT(r.name ORDER BY r.id SEPARATOR \', \') AS role_names
          FROM champions c
          LEFT JOIN champion_roles cr ON cr.champion_id = c.id
          LEFT JOIN roles r ON r.id = cr.role_id';
$params = [];
if ($selectedRole > 0) {
    $query .= ' WHERE c.id IN (SELECT champion_id FROM champion_roles WHERE role_id = ?)';
    $params[] = $selectedRole;
}
$query .= ' GROUP BY c.id, c.name, c.title, c.image_path ORDER BY c.name';

$stmt = db()->prepare($query);
$stmt->execute($params);
$champions = $stmt->fetchAll();

$championsListHtml = '';
foreach ($champions as $champion) {
    $championsListHtml .= '<article class="champion-card">';
    $championsListHtml .= '<img src="' . e($champion['image_path']) . '" alt="' . e($champion['name']) . '" width="96" height="96" />';
    $championsListHtml .= '<h3>' . e($champion['name']) . '</h3>';
    if ($champion['title'] !== null && $champion['title'] !== '') {
        $championsListHtml .= '<p>' . e($champion['title']) . '</p>';
    }
    $championsListHtml .= '<p><strong>Ruoli:</strong> ' . e($champion['role_names'] !== null ? $champion['role_names'] : 'non assegnati') . '</p>';
    $championsListHtml .= '</article>';
}
if ($championsListHtml === '') {
    $championsListHtml = '<p>Nessun campione trovato per il ruolo scelto.</p>';
} else {
    $championsListHtml = '<div class="card-grid">' . $championsListHtml . '</div>';
}

render_page('templates/champions.html', [
    'title'       => 'Campioni - Rift Academy',
    'description' => 'Catalogo dei campioni di League of Legends filtrabile per ruolo.',
], [
    'role_options'   => $roleOptionsHtml,
    'champions_list' => $championsListHtml,
]);
