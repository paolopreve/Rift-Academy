<?php
// index.php - Home: ultime guide e ricerca rapida (pubblica)
require_once 'includes/auth.php';
require_once 'includes/db.php';

// Leggere le ultime guide (ORDER BY created_at DESC LIMIT 6) con prepared statement
$stmt = db()->prepare('SELECT g.id, g.title, g.strategy_text, g.created_at, c.name AS champion_name, r.name AS role_name, u.username AS author_name
                       FROM guides g
                       JOIN champions c ON g.champion_id = c.id
                       JOIN roles r ON g.role_id = r.id
                       JOIN users u ON g.user_id = u.id
                       WHERE u.status = ?
                       ORDER BY g.created_at DESC
                       LIMIT 6');
$stmt->execute(['active']);
$latestGuides = $stmt->fetchAll();

// Costruire l'HTML delle card, usando e() per ogni dato utente
$latestGuidesHtml = '';
foreach ($latestGuides as $guide) {
    $latestGuidesHtml .= '<div class="guide-card">';
    $latestGuidesHtml .= '<h2>' . e($guide['title']) . '</h2>';
    $latestGuidesHtml .= '<p>' . e($guide['strategy_text']) . '</p>';
    $latestGuidesHtml .= '<p><strong>Campione:</strong> ' . e($guide['champion_name']) . '</p>';
    $latestGuidesHtml .= '<p><strong>Ruolo:</strong> ' . e($guide['role_name']) . '</p>';
    $latestGuidesHtml .= '<p><strong>Autore:</strong> ' . e($guide['author_name']) . '</p>';
    $latestGuidesHtml .= '<p><strong>Data:</strong> ' . e($guide['created_at']) . '</p>';
    $latestGuidesHtml .= '<a href="guide.php?id=' . e($guide['id']) . '">Leggi la guida completa</a>';
    $latestGuidesHtml .= '</div>';
}

render_page('templates/index.html', [
    'title'       => 'Rift Academy - Guide e build per League of Legends',
    'description' => 'Guide, build e rune per campione e ruolo, scritte dalla community di League of Legends.',
], [
    'latest_guides' => $latestGuidesHtml,
]);