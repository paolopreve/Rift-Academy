<?php
// guide_detail.php - Dettaglio guida: build, rune, strategia, commenti, voti (pubblica)
require_once 'includes/auth.php';
require_once 'includes/db.php';

$guide_id = get_int('id');

$stmt = db()->prepare('SELECT g.id, g.user_id, g.title, g.strategy_text, g.patch, g.created_at, g.updated_at,
                              c.name AS champion_name, r.name AS role_name, u.username AS author_name
                       FROM guides g
                       JOIN champions c ON g.champion_id = c.id
                       JOIN roles r ON g.role_id = r.id
                       JOIN users u ON g.user_id = u.id
                       WHERE g.id = ?');
$stmt->execute([$guide_id]);
$guide = $stmt->fetch();

if (!$guide) {
    require '404.php';
    exit;
}

$user = current_user();
$isOwner = ($user !== null && (int) $guide['user_id'] === $user['id']);
$gid = e($guide['id']);

// Media dei voti e numero di voti
$stmt = db()->prepare('SELECT AVG(value) AS avg_value, COUNT(*) AS n FROM ratings WHERE guide_id = ?');
$stmt->execute([$guide_id]);
$ratingRow = $stmt->fetch();

// Voto già dato dall'utente connesso
$myRating = 0;
if ($user !== null) {
    $stmt = db()->prepare('SELECT value FROM ratings WHERE guide_id = ? AND user_id = ?');
    $stmt->execute([$guide_id, $user['id']]);
    $row = $stmt->fetch();
    if ($row) {
        $myRating = (int) $row['value'];
    }
}

// Preferito già salvato dall'utente connesso
$isFavorite = false;
if ($user !== null) {
    $stmt = db()->prepare('SELECT guide_id FROM favorites WHERE user_id = ? AND guide_id = ?');
    $stmt->execute([$user['id'], $guide_id]);
    $isFavorite = (bool) $stmt->fetch();
}

// Oggetti e rune
$stmt = db()->prepare('SELECT i.name, i.image_path FROM guide_items gi JOIN items i ON gi.item_id = i.id WHERE gi.guide_id = ? ORDER BY gi.position');
$stmt->execute([$guide_id]);
$items = $stmt->fetchAll();

$stmt = db()->prepare('SELECT r.name, r.tree, r.image_path FROM guide_runes gr JOIN runes r ON gr.rune_id = r.id WHERE gr.guide_id = ? ORDER BY gr.position');
$stmt->execute([$guide_id]);
$runes = $stmt->fetchAll();

// Commenti
$stmt = db()->prepare('SELECT cm.id, cm.user_id, cm.body, cm.created_at, u.username
                       FROM comments cm JOIN users u ON cm.user_id = u.id
                       WHERE cm.guide_id = ? ORDER BY cm.created_at ASC, cm.id ASC');
$stmt->execute([$guide_id]);
$comments = $stmt->fetchAll();

// Breadcrumb
$breadcrumbHtml = '<ol><li><a href="index.php">Home</a></li><li aria-current="page">' . e($guide['title']) . '</li></ol>';

// Metadati (testo semplice, dentro un paragrafo)
$metaText = 'Campione: ' . $guide['champion_name'] . ' | Ruolo: ' . $guide['role_name'] . ' | Patch: ' . $guide['patch']
    . ' | Autore: ' . $guide['author_name'] . ' | Pubblicata il ' . $guide['created_at'];
if ($guide['updated_at'] !== null) {
    $metaText .= ' | Aggiornata il ' . $guide['updated_at'];
}

// Valutazione
if ((int) $ratingRow['n'] > 0) {
    $ratingHtml = '<p>Voto medio: ' . e(number_format((float) $ratingRow['avg_value'], 1, ',', '')) . ' su 5 (' . e($ratingRow['n']) . ' voti)</p>';
} else {
    $ratingHtml = '<p>Questa guida non ha ancora voti.</p>';
}
if ($user !== null && !$isOwner) {
    $options = '';
    for ($v = 1; $v <= 5; $v++) {
        $options .= '<option value="' . $v . '"' . ($v === $myRating ? ' selected="selected"' : '') . '>' . $v . '</option>';
    }
    $ratingHtml .= '<form action="rate_guide.php" method="post">' . csrf_field()
        . '<input type="hidden" name="guide_id" value="' . $gid . '" />'
        . '<p><label for="rating-value">Il tuo voto (da 1 a 5)</label>'
        . '<select id="rating-value" name="value">' . $options . '</select></p>'
        . '<p><button type="submit">Invia voto</button></p></form>';
} elseif ($user === null) {
    $ratingHtml .= '<p><a href="login.php">Accedi</a> per votare questa guida.</p>';
}

// Strategia: testo libero, escape e a capo
$strategyHtml = '<p>' . nl2br(e($guide['strategy_text'])) . '</p>';

// Build e rune
$buildHtml = '<h3>Oggetti</h3>';
if (empty($items)) {
    $buildHtml .= '<p>Nessun oggetto indicato.</p>';
} else {
    $buildHtml .= '<ul>';
    foreach ($items as $item) {
        $buildHtml .= '<li><img src="' . e($item['image_path']) . '" alt="" width="32" height="32" /> ' . e($item['name']) . '</li>';
    }
    $buildHtml .= '</ul>';
}
$buildHtml .= '<h3>Rune</h3>';
if (empty($runes)) {
    $buildHtml .= '<p>Nessuna runa indicata.</p>';
} else {
    $buildHtml .= '<ul>';
    foreach ($runes as $rune) {
        $buildHtml .= '<li><img src="' . e($rune['image_path']) . '" alt="" width="32" height="32" /> ' . e($rune['name']) . ' (' . e($rune['tree']) . ')</li>';
    }
    $buildHtml .= '</ul>';
}

// Azioni sulla guida: preferito, segnala, modifica, elimina
$actionsHtml = '';
if ($user !== null) {
    $actionsHtml .= '<form action="favorite_toggle.php" method="post">' . csrf_field()
        . '<input type="hidden" name="guide_id" value="' . $gid . '" />'
        . '<button type="submit">' . ($isFavorite ? 'Rimuovi dai preferiti' : 'Aggiungi ai preferiti') . '</button></form>';

    $actionsHtml .= '<form action="report_content.php" method="post">' . csrf_field()
        . '<input type="hidden" name="guide_id" value="' . $gid . '" />'
        . '<p><label for="report-guide-reason">Motivo della segnalazione della guida (obbligatorio)</label>'
        . '<input id="report-guide-reason" name="reason" type="text" required="required" maxlength="255" /></p>'
        . '<p><button type="submit">Segnala guida</button></p></form>';

    if (is_owner_or_admin((int) $guide['user_id'])) {
        $actionsHtml .= '<p><a href="edit_guide.php?id=' . $gid . '">Modifica guida</a></p>';
        $actionsHtml .= '<form action="delete_guide.php" method="post">' . csrf_field()
            . '<input type="hidden" name="guide_id" value="' . $gid . '" />'
            . '<button type="submit">Elimina guida</button></form>';
    }
}

// Elenco commenti
if (empty($comments)) {
    $commentsHtml = '<p>Non ci sono ancora commenti.</p>';
} else {
    $commentsHtml = '<ul>';
    foreach ($comments as $comment) {
        $cid = e($comment['id']);
        $commentsHtml .= '<li><p><strong>' . e($comment['username']) . '</strong> - ' . e($comment['created_at']) . '</p>';
        $commentsHtml .= '<p>' . nl2br(e($comment['body'])) . '</p>';
        if ($user !== null) {
            if (is_owner_or_admin((int) $comment['user_id'])) {
                $commentsHtml .= '<form action="comment_action.php" method="post">' . csrf_field()
                    . '<input type="hidden" name="action" value="delete" />'
                    . '<input type="hidden" name="comment_id" value="' . $cid . '" />'
                    . '<button type="submit">Elimina commento di ' . e($comment['username']) . '</button></form>';
            }
            $commentsHtml .= '<form action="report_content.php" method="post">' . csrf_field()
                . '<input type="hidden" name="comment_id" value="' . $cid . '" />'
                . '<p><label for="report-comment-' . $cid . '">Motivo della segnalazione del commento di ' . e($comment['username']) . '</label>'
                . '<input id="report-comment-' . $cid . '" name="reason" type="text" required="required" maxlength="255" /></p>'
                . '<p><button type="submit">Segnala commento</button></p></form>';
        }
        $commentsHtml .= '</li>';
    }
    $commentsHtml .= '</ul>';
}

// Form per commentare
if ($user !== null) {
    $commentFormHtml = '<form action="comment_action.php" method="post" data-validate="data-validate">' . csrf_field()
        . '<input type="hidden" name="action" value="create" />'
        . '<input type="hidden" name="guide_id" value="' . $gid . '" />'
        . '<p><label for="body">Il tuo commento (obbligatorio)</label>'
        . '<textarea id="body" name="body" rows="4" cols="60" required="required" maxlength="2000"></textarea></p>'
        . '<p><button type="submit">Pubblica commento</button></p></form>';
} else {
    $commentFormHtml = '<p><a href="login.php">Accedi</a> per commentare.</p>';
}

render_page('templates/guide_detail.html', [
    'title'       => $guide['title'] . ' - Rift Academy',
    'description' => 'Guida di ' . $guide['champion_name'] . ' (' . $guide['role_name'] . ') per League of Legends.',
], [
    'breadcrumb'     => $breadcrumbHtml,
    'guide_title'    => e($guide['title']),
    'guide_meta'     => e($metaText),
    'guide_actions'  => $actionsHtml,
    'rating'         => $ratingHtml,
    'guide_strategy' => $strategyHtml,
    'guide_build'    => $buildHtml,
    'comments'       => $commentsHtml,
    'comment_form'   => $commentFormHtml,
]);
