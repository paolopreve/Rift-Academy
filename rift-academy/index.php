<?php
// index.php - Home: ultime guide e ricerca rapida (pubblica)
require_once 'includes/auth.php';
require_once 'includes/db.php';

// Ultime 6 guide di utenti attivi
$stmt = db()->prepare('SELECT g.id, g.title, g.strategy_text, g.created_at, c.name AS champion_name, c.image_path AS champion_image, r.name AS role_name, u.username AS author_name
                       FROM guides g
                       JOIN champions c ON g.champion_id = c.id
                       JOIN roles r ON g.role_id = r.id
                       JOIN users u ON g.user_id = u.id
                       WHERE u.status = ?
                       ORDER BY g.created_at DESC
                       LIMIT 3');
$stmt->execute(['active']);
$latestGuides = $stmt->fetchAll();

// Card delle ultime guide (colonna di destra): ogni dato utente passa da e()
$latestGuidesHtml = '';
foreach ($latestGuides as $guide) {
    $latestGuidesHtml .= '<article class="guide-card">';
    // Immagine del campione solo decorativa: il nome è scritto subito sotto
    $latestGuidesHtml .= '<div class="guide-card-banner"><img src="' . e(img_small($guide['champion_image'])) . '" alt="" width="480" height="283" loading="lazy" decoding="async" />';
    $latestGuidesHtml .= '<p class="tags"><span class="tag tag-role" lang="en">' . e($guide['role_name']) . '</span> <span class="tag">' . e($guide['champion_name']) . '</span></p></div>';
    $latestGuidesHtml .= '<div class="guide-card-body">';
    $latestGuidesHtml .= '<h3>' . en(e($guide['title'])) . '</h3>';
    $latestGuidesHtml .= '<p class="excerpt">' . en(e($guide['strategy_text'])) . '</p>';
    $latestGuidesHtml .= '<p class="guide-card-foot"><span><strong>' . e($guide['author_name']) . '</strong> · ' . e(data_breve($guide['created_at'])) . '</span>';
    $latestGuidesHtml .= ' <a href="guide_detail.php?id=' . e($guide['id']) . '">Leggi<span class="visually-hidden"> la guida ' . en(e($guide['title'])) . '</span></a></p>';
    $latestGuidesHtml .= '</div></article>';
}
if ($latestGuidesHtml === '') {
    $latestGuidesHtml = '<p>Non ci sono ancora guide pubblicate.</p>';
}

// Riquadri "Sfoglia per ruolo": icona (decorativa) + nome del ruolo
$roles = db()->query('SELECT id, name FROM roles ORDER BY id')->fetchAll();
$roleLinksHtml = '';
foreach ($roles as $role) {
    // Il file dell'icona ha il nome del ruolo in minuscolo (images/roles/mid.svg)
    $icon = 'images/roles/' . strtolower(preg_replace('/[^A-Za-z]/', '', $role['name'])) . '.svg';
    $roleLinksHtml .= '<li><a href="search.php?role=' . e($role['id']) . '"><img src="' . e($icon) . '" alt="" width="24" height="24" /> '
        . '<span lang="en">' . e($role['name']) . '</span></a></li>';
}

render_page('templates/index.html', [
    'title'       => 'Rift Academy - Guide e build per League of Legends',
    'description' => 'Guide, build e rune per campione e ruolo, scritte dalla community di League of Legends.',
    'keywords'    => 'League of Legends, guide, build, rune, matchup, rank, Top, Jungle, Mid, Bot, Support',
], [
    'latest_guides' => $latestGuidesHtml,
    'role_links'    => $roleLinksHtml,
]);
