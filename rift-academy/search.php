<?php
// search.php - Risultati della ricerca testuale su guide e campioni (pubblica)
require_once 'includes/auth.php';
require_once 'includes/db.php';

// Leggere 'q' da GET, validare
$searchQuery = trim(get_string('q'));
$resultsHtml = '';

if (!empty($searchQuery)) {
    // Cercare con prepared statement (LIKE)
    // Usare LIKE per cercare nel titolo delle guide e nel nome dei campioni
    $searchPattern = '%' . $searchQuery . '%';

    // Query per le guide
    $guideStmt = db()->prepare("
        SELECT g.id, g.title, c.name AS champion_name, r.name AS role_name
        FROM guides g
        JOIN champions c ON g.champion_id = c.id
        JOIN roles r ON g.role_id = r.id
        WHERE g.title LIKE ?
    ");
    $guideStmt->execute([$searchPattern]);
    $guides = $guideStmt->fetchAll();

    // Query per i campioni
    $championStmt = db()->prepare("
        SELECT c.id, c.name AS champion_name
        FROM champions c
        WHERE c.name LIKE ?
    ");
    $championStmt->execute([$searchPattern]);
    $champions = $championStmt->fetchAll();

    // Costruire l'HTML dei risultati
    if (!empty($guides)) {
        $resultsHtml .= '<h2>Risultati delle guide</h2>';
        $resultsHtml .= '<ul>';
        foreach ($guides as $guide) {
            $resultsHtml .= '<li>';
            $resultsHtml .= '<a href="guide.php?id=' . e($guide['id']) . '">' . e($guide['title']) . '</a>';
            $resultsHtml .= ' - Campione: ' . e($guide['champion_name']) . ', Ruolo: ' . e($guide['role_name']);
            $resultsHtml .= '</li>';
        }
        $resultsHtml .= '</ul>';
    }

    if (!empty($champions)) {
        $resultsHtml .= '<h2>Risultati dei campioni</h2>';
        $resultsHtml .= '<ul>';
        foreach ($champions as $champion) {
            $resultsHtml .= '<li>';
            $resultsHtml .= '<a href="champion.php?id=' . e($champion['id']) . '">' . e($champion['champion_name']) . '</a>';
            $resultsHtml .= '</li>';
        }
        $resultsHtml .= '</ul>';
    }

    if (empty($guides) && empty($champions)) {
        $resultsHtml .= '<p>Nessun risultato trovato per la query: ' . e($searchQuery) . '</p>';
    }
}

render_page('templates/search.html', [
    'title'       => 'Cerca - Rift Academy',
    'description' => 'Risultati della ricerca su guide e campioni.',
], [
    'query' => $searchQuery,
    'results' => $resultsHtml,
]);