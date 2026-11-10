<?php
// search.php - Ricerca di guide per titolo e/o corsia, e di campioni per nome (pubblica)
require_once 'includes/auth.php';
require_once 'includes/db.php';

$searchQuery = get_string('q');
$selectedRole = get_int('role');

// Il ruolo scelto viene validato contro la tabella roles
$roles = db()->query('SELECT id, name FROM roles ORDER BY id')->fetchAll();
$roleName = '';
$roleOptionsHtml = '<option value="0">Tutte le corsie</option>';
foreach ($roles as $role) {
    $isSelected = ((int) $role['id'] === $selectedRole);
    if ($isSelected) {
        $roleName = $role['name'];
    }
    $roleOptionsHtml .= '<option value="' . e($role['id']) . '" lang="en"' . ($isSelected ? ' selected="selected"' : '') . '>' . e($role['name']) . '</option>';
}
if ($roleName === '') {
    $selectedRole = 0;
}

$resultsHtml = '';

// Senza filtri la pagina elenca tutte le guide
{
    // Titolo della sezione risultati
    if ($searchQuery === '' && $roleName === '') {
        $resultsTitle = 'Tutte le guide';
    } elseif ($searchQuery !== '' && $roleName !== '') {
        $resultsTitle = 'Risultati per "' . e($searchQuery) . '" nella corsia <span lang="en">' . e($roleName) . '</span>';
    } elseif ($roleName !== '') {
        $resultsTitle = 'Guide per la corsia <span lang="en">' . e($roleName) . '</span>';
    } else {
        $resultsTitle = 'Risultati per "' . e($searchQuery) . '"';
    }

    // Query delle guide costruita a pezzi, sempre con parametri
    $sql = 'SELECT g.id, g.title, g.created_at, c.name AS champion_name, c.image_path AS champion_image, r.name AS role_name, u.username AS author_name
            FROM guides g
            JOIN champions c ON g.champion_id = c.id
            JOIN roles r ON g.role_id = r.id
            JOIN users u ON g.user_id = u.id
            WHERE 1 = 1';
    $params = [];
    $searchPattern = '';
    if ($searchQuery !== '') {
        // Escape di \, % e _ nel testo cercato, poi il pattern viene passato come parametro
        $escaped = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $searchQuery);
        $searchPattern = '%' . $escaped . '%';
        $sql .= ' AND g.title LIKE ?';
        $params[] = $searchPattern;
    }
    if ($selectedRole > 0) {
        $sql .= ' AND g.role_id = ?';
        $params[] = $selectedRole;
    }
    $sql .= ' ORDER BY g.created_at DESC';

    $guideStmt = db()->prepare($sql);
    $guideStmt->execute($params);
    $guides = $guideStmt->fetchAll();

    // I campioni si cercano solo per nome
    $champions = [];
    if ($searchQuery !== '') {
        $championStmt = db()->prepare('SELECT id, name FROM champions WHERE name LIKE ? ORDER BY name');
        $championStmt->execute([$searchPattern]);
        $champions = $championStmt->fetchAll();
    }

    if (!empty($guides)) {
        $resultsHtml .= '<ul class="guide-list">';
        foreach ($guides as $guide) {
            $resultsHtml .= '<li class="guide-item"><img src="' . e(img_small($guide['champion_image'])) . '" alt="" width="64" height="64" loading="lazy" decoding="async" />';
            $resultsHtml .= '<div><a href="guide_detail.php?id=' . e($guide['id']) . '">' . en(e($guide['title'])) . '</a>';
            $resultsHtml .= '<p><span class="tag tag-role" lang="en">' . e($guide['role_name']) . '</span> ' . e($guide['champion_name'])
                . ' · di ' . e($guide['author_name']) . ' · ' . e(data_breve($guide['created_at'])) . '</p></div></li>';
        }
        $resultsHtml .= '</ul>';
    }

    if (!empty($champions)) {
        $resultsHtml .= '<h3>Campioni trovati</h3><ul>';
        foreach ($champions as $champion) {
            $resultsHtml .= '<li>' . e($champion['name']) . '</li>';
        }
        $resultsHtml .= '</ul>';
        $resultsHtml .= '<p><a href="champions.php">Vai al catalogo dei campioni</a></p>';
    }

    if (empty($guides) && empty($champions)) {
        $resultsHtml = '<p>Nessun risultato trovato. Prova con un altro nome o scegli un\'altra corsia.</p>';
    }
}

render_page('templates/search.html', [
    'title'       => 'Cerca - Rift Academy',
    'description' => 'Ricerca di guide per titolo, campione o corsia.',
    'keywords'    => 'League of Legends, guide, ricerca, campioni, corsia, build',
], [
    'query'         => e($searchQuery),
    'role_options'  => $roleOptionsHtml,
    'results_title' => $resultsTitle,
    'results'       => $resultsHtml,
]);
