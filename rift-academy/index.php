<?php
// index.php - Home: ultime guide e ricerca rapida (pubblica)
require_once 'includes/auth.php';
require_once 'includes/db.php';

// Ultime 6 guide di utenti attivi
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

// HTML delle card: ogni dato utente passa da e()
$latestGuidesHtml = '';
foreach ($latestGuides as $guide) {
    $latestGuidesHtml .= '<article class="guide-card">';
    $latestGuidesHtml .= '<h3>' . e($guide['title']) . '</h3>';
    $latestGuidesHtml .= '<p>' . e($guide['strategy_text']) . '</p>';
    $latestGuidesHtml .= '<p><strong>Campione:</strong> ' . e($guide['champion_name']) . '</p>';
    $latestGuidesHtml .= '<p><strong>Ruolo:</strong> ' . e($guide['role_name']) . '</p>';
    $latestGuidesHtml .= '<p><strong>Autore:</strong> ' . e($guide['author_name']) . '</p>';
    $latestGuidesHtml .= '<p><strong>Data:</strong> ' . e($guide['created_at']) . '</p>';
    $latestGuidesHtml .= '<p><a href="guide_detail.php?id=' . e($guide['id']) . '">Leggi la guida completa: ' . e($guide['title']) . '</a></p>';
    $latestGuidesHtml .= '</article>';
}
if ($latestGuidesHtml === '') {
    $latestGuidesHtml = '<p>Non ci sono ancora guide pubblicate.</p>';
}

render_page('templates/index.html', [
    'title'       => 'Rift Academy - Guide e build per League of Legends',
    'description' => 'Guide, build e rune per campione e ruolo, scritte dalla community di League of Legends.',
], [
    'latest_guides' => $latestGuidesHtml,
]);
