<?php
// admin_dashboard.php - Moderazione di guide, commenti e segnalazioni (admin)
require_once 'includes/auth.php';
require_once 'includes/db.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = post_str('action');

    if ($action === 'delete_guide') {
        $stmt = db()->prepare('DELETE FROM guides WHERE id = ?');
        $stmt->execute([(int) post_str('guide_id')]);
        flash_set('Guida eliminata.');
    } elseif ($action === 'delete_comment') {
        $stmt = db()->prepare('DELETE FROM comments WHERE id = ?');
        $stmt->execute([(int) post_str('comment_id')]);
        flash_set('Commento eliminato.');
    } elseif ($action === 'close_report') {
        $stmt = db()->prepare('UPDATE reports SET status = ? WHERE id = ?');
        $stmt->execute(['closed', (int) post_str('report_id')]);
        flash_set('Segnalazione chiusa.');
    } else {
        flash_set('Azione non valida.');
    }
    redirect('admin_dashboard.php');
}

// Segnalazioni aperte con il contenuto segnalato
$stmt = db()->prepare('SELECT rp.id, rp.reason, rp.created_at, rp.guide_id, rp.comment_id,
                              u.username AS reporter, g.title AS guide_title,
                              cm.body AS comment_body, cm.guide_id AS comment_guide_id
                       FROM reports rp
                       JOIN users u ON rp.reporter_id = u.id
                       LEFT JOIN guides g ON rp.guide_id = g.id
                       LEFT JOIN comments cm ON rp.comment_id = cm.id
                       WHERE rp.status = ?
                       ORDER BY rp.created_at DESC');
$stmt->execute(['open']);
$reports = $stmt->fetchAll();

if (empty($reports)) {
    $reportsHtml = '<p>Non ci sono segnalazioni aperte.</p>';
} else {
    $reportsHtml = '<ul>';
    foreach ($reports as $rep) {
        $rid = e($rep['id']);
        $reportsHtml .= '<li><p>Segnalazione di ' . e($rep['reporter']) . ' del ' . e($rep['created_at']) . ': ' . e($rep['reason']) . '</p>';
        if ($rep['guide_id'] !== null) {
            $reportsHtml .= '<p>Guida: <a href="guide_detail.php?id=' . e($rep['guide_id']) . '">' . en(e($rep['guide_title'])) . '</a></p>';
            $reportsHtml .= '<form action="admin_dashboard.php" method="post">' . csrf_field()
                . '<input type="hidden" name="action" value="delete_guide" />'
                . '<input type="hidden" name="guide_id" value="' . e($rep['guide_id']) . '" />'
                . '<button type="submit">Elimina la guida segnalata (segnalazione ' . $rid . ')</button></form>';
        } else {
            $reportsHtml .= '<p>Commento: ' . e($rep['comment_body']) . ' - <a href="guide_detail.php?id=' . e($rep['comment_guide_id']) . '">vai alla guida</a></p>';
            $reportsHtml .= '<form action="admin_dashboard.php" method="post">' . csrf_field()
                . '<input type="hidden" name="action" value="delete_comment" />'
                . '<input type="hidden" name="comment_id" value="' . e($rep['comment_id']) . '" />'
                . '<button type="submit">Elimina il commento segnalato (segnalazione ' . $rid . ')</button></form>';
        }
        $reportsHtml .= '<form action="admin_dashboard.php" method="post">' . csrf_field()
            . '<input type="hidden" name="action" value="close_report" />'
            . '<input type="hidden" name="report_id" value="' . $rid . '" />'
            . '<button type="submit">Chiudi la segnalazione ' . $rid . '</button></form></li>';
    }
    $reportsHtml .= '</ul>';
}

// Ultime guide
$guides = db()->query('SELECT g.id, g.title, u.username AS author_name
                       FROM guides g JOIN users u ON g.user_id = u.id
                       ORDER BY g.created_at DESC LIMIT 10')->fetchAll();

if (empty($guides)) {
    $latestHtml = '<p>Non ci sono guide.</p>';
} else {
    $latestHtml = '<ul>';
    foreach ($guides as $guide) {
        $latestHtml .= '<li><a href="guide_detail.php?id=' . e($guide['id']) . '">' . en(e($guide['title'])) . '</a> (di ' . e($guide['author_name']) . ')'
            . '<form action="admin_dashboard.php" method="post">' . csrf_field()
            . '<input type="hidden" name="action" value="delete_guide" />'
            . '<input type="hidden" name="guide_id" value="' . e($guide['id']) . '" />'
            . '<button type="submit">Elimina ' . e($guide['title']) . '</button></form></li>';
    }
    $latestHtml .= '</ul>';
}

render_page('templates/admin_dashboard.html', [
    'title'       => 'Moderazione - Rift Academy',
    'description' => 'Pannello di moderazione.',
], [
    'csrf'          => '',
    'reports'       => $reportsHtml,
    'latest_guides' => $latestHtml,
]);
