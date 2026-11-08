<?php
// search.php - Risultati della ricerca testuale su guide e campioni (pubblica)
require_once 'includes/auth.php';
require_once 'includes/db.php';

$searchQuery = get_string('q');
$resultsHtml = '';

if ($searchQuery === '') {
    $resultsHtml = '<p>Inserisci un termine da cercare nel campo di ricerca.</p>';
} else {
    // Escape di \, % e _ nel testo cercato, poi il pattern viene passato come parametro
    $escaped = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $searchQuery);
    $searchPattern = '%' . $escaped . '%';

    $guideStmt = db()->prepare('SELECT g.id, g.title, c.name AS champion_name, r.name AS role_name
                                FROM guides g
                                JOIN champions c ON g.champion_id = c.id
                                JOIN roles r ON g.role_id = r.id
                                WHERE g.title LIKE ?
                                ORDER BY g.created_at DESC');
    $guideStmt->execute([$searchPattern]);
    $guides = $guideStmt->fetchAll();

    $championStmt = db()->prepare('SELECT id, name FROM champions WHERE name LIKE ? ORDER BY name');
    $championStmt->execute([$searchPattern]);
    $champions = $championStmt->fetchAll();

    if (!empty($guides)) {
        $resultsHtml .= '<h2>Guide</h2><ul>';
        foreach ($guides as $guide) {
            $resultsHtml .= '<li><a href="guide_detail.php?id=' . e($guide['id']) . '">' . e($guide['title']) . '</a>';
            $resultsHtml .= ' - Campione: ' . e($guide['champion_name']) . ', Ruolo: ' . e($guide['role_name']) . '</li>';
        }
        $resultsHtml .= '</ul>';
    }

    if (!empty($champions)) {
        $resultsHtml .= '<h2>Campioni</h2><ul>';
        foreach ($champions as $champion) {
            $resultsHtml .= '<li>' . e($champion['name']) . '</li>';
        }
        $resultsHtml .= '</ul>';
        $resultsHtml .= '<p><a href="champions.php">Vai al catalogo dei campioni</a></p>';
    }

    if (empty($guides) && empty($champions)) {
        $resultsHtml = '<p>Nessun risultato trovato.</p>';
    }
}

render_page('templates/search.html', [
    'title'       => 'Cerca - Rift Academy',
    'description' => 'Risultati della ricerca su guide e campioni.',
], [
    'query'   => e($searchQuery),
    'results' => $resultsHtml,
]);
