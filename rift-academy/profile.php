<?php
// profile.php - Profilo: proprie guide e preferiti (utente registrato)
require_once 'includes/auth.php';
require_once 'includes/db.php';

require_login();

$userId = current_user()['id'];

// Guide dell'utente
$stmt = db()->prepare('SELECT g.id, g.title, g.created_at, c.name AS champion_name
                       FROM guides g JOIN champions c ON g.champion_id = c.id
                       WHERE g.user_id = ? ORDER BY g.created_at DESC');
$stmt->execute([$userId]);
$myGuides = $stmt->fetchAll();

if (empty($myGuides)) {
    $myGuidesHtml = '<p>Non hai ancora pubblicato guide. <a href="create_guide.php">Crea la tua prima guida</a>.</p>';
} else {
    $myGuidesHtml = '<ul>';
    foreach ($myGuides as $guide) {
        $gid = e($guide['id']);
        $myGuidesHtml .= '<li><a href="guide_detail.php?id=' . $gid . '">' . en(e($guide['title'])) . '</a>'
            . ' (' . e($guide['champion_name']) . ', ' . e($guide['created_at']) . ')'
            . ' - <a href="edit_guide.php?id=' . $gid . '">Modifica ' . e($guide['title']) . '</a>'
            . '<form action="delete_guide.php" method="post">' . csrf_field()
            . '<input type="hidden" name="guide_id" value="' . $gid . '" />'
            . '<button type="submit">Elimina ' . e($guide['title']) . '</button></form></li>';
    }
    $myGuidesHtml .= '</ul>';
}

// Guide preferite
$stmt = db()->prepare('SELECT g.id, g.title, c.name AS champion_name, u.username AS author_name
                       FROM favorites f
                       JOIN guides g ON f.guide_id = g.id
                       JOIN champions c ON g.champion_id = c.id
                       JOIN users u ON g.user_id = u.id
                       WHERE f.user_id = ? ORDER BY f.created_at DESC');
$stmt->execute([$userId]);
$favorites = $stmt->fetchAll();

if (empty($favorites)) {
    $favoritesHtml = '<p>Non hai ancora guide preferite.</p>';
} else {
    $favoritesHtml = '<ul>';
    foreach ($favorites as $fav) {
        $favoritesHtml .= '<li><a href="guide_detail.php?id=' . e($fav['id']) . '">' . en(e($fav['title'])) . '</a>'
            . ' (' . e($fav['champion_name']) . ', di ' . e($fav['author_name']) . ')</li>';
    }
    $favoritesHtml .= '</ul>';
}

render_page('templates/profile.html', [
    'title'       => 'Il tuo profilo - Rift Academy',
    'description' => 'Le tue guide e i tuoi preferiti.',
], [
    'my_guides' => $myGuidesHtml,
    'favorites' => $favoritesHtml,
]);
