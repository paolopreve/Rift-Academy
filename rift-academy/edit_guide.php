<?php
// edit_guide.php - Modifica di una guida esistente (autore o admin)
require_once 'includes/auth.php';
require_once 'includes/db.php';

require_login();

$guide_id = get_int('id');

$stmt = db()->prepare('SELECT * FROM guides WHERE id = ?');
$stmt->execute([$guide_id]);
$guide = $stmt->fetch();

if (!$guide || !is_owner_or_admin((int) $guide['user_id'])) {
    http_response_code(403);
    echo 'Accesso negato.';
    exit;
}

$csrf = csrf_field();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $title = post_str('title');
    $strategy_text = post_str('strategy_text');
    $patch = post_str('patch');
    $champion_id = get_int('champion_id');
    $role_id = get_int('role_id');
    $item_ids = array_filter(post_arr('items'), 'is_numeric');
    $rune_ids = array_filter(post_arr('runes'), 'is_numeric');
    if (empty($title)) {
        $errors[] = 'Titolo obbligatorio.';
    }
    if (empty($strategy_text)) {
        $errors[] = 'Testo della strategia obbligatorio.';
    }
    if (empty($patch)) {
        $errors[] = 'Patch obbligatoria.';
    }
    if ($champion_id <= 0) {
        $errors[] = 'Seleziona un campione valido.';
    }
    if ($role_id <= 0) {
        $errors[] = 'Seleziona un ruolo valido.';
    }

    if (empty($errors)) {
        try {
            db()->beginTransaction();

            $updateStmt = db()->prepare('UPDATE guides SET title = ?, strategy_text = ?, patch = ?, champion_id = ?, role_id = ?, updated_at = NOW() WHERE id = ?');
            $updateStmt->execute([$title, $strategy_text, $patch, $champion_id, $role_id, $guide_id]);

            $deleteItemsStmt = db()->prepare('DELETE FROM guide_items WHERE guide_id = ?');
            $deleteItemsStmt->execute([$guide_id]);

            $insertItemsStmt = db()->prepare('INSERT INTO guide_items (guide_id, item_id) VALUES (?, ?)');
            foreach ($item_ids as $item_id) {
                $insertItemsStmt->execute([$guide_id, $item_id]);
            }

            $deleteRunesStmt = db()->prepare('DELETE FROM guide_runes WHERE guide_id = ?');
            $deleteRunesStmt->execute([$guide_id]);

            $insertRunesStmt = db()->prepare('INSERT INTO guide_runes (guide_id, rune_id) VALUES (?, ?)');
            foreach ($rune_ids as $rune_id) {
                $insertRunesStmt->execute([$guide_id, $rune_id]);
            }

            db()->commit();

            flash_set('Guida aggiornata con successo.');
            redirect('guide.php?id=' . $guide_id);
        } catch (Exception $e) {
            db()->rollBack();
            error_log($e->getMessage());
            $errors[] = "Errore durante l'aggiornamento della guida.";
        }
    }
}

$championStmt = db()->query('SELECT id, name FROM champions');
$champions = $championStmt->fetchAll(PDO::FETCH_ASSOC);
$champion_options = '<option value="">Seleziona un campione</option>';
foreach ($champions as $champion) {
    $selected = ($champion['id'] === (int) $guide['champion_id']) ? ' selected="selected"' : '';
    $champion_options .= '<option value="' . e($champion['id']) . '"' . $selected . '>' . e($champion['name']) . '</option>';
}

$roleStmt = db()->query('SELECT id, name FROM roles');
$roles = $roleStmt->fetchAll(PDO::FETCH_ASSOC);
$role_options = '<option value="">Seleziona un ruolo</option>';
foreach ($roles as $role) {
    $selected = ($role['id'] === (int) $guide['role_id']) ? ' selected="selected"' : '';
    $role_options .= '<option value="' . e($role['id']) . '"' . $selected . '>' . e($role['name']) . '</option>';
}

$itemStmt = db()->query('SELECT id, name FROM items');
$items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);

$stmtItems = db()->prepare('SELECT item_id FROM guide_items WHERE guide_id = ?');
$stmtItems->execute([$guide_id]);
$selected_items = array_column($stmtItems->fetchAll(PDO::FETCH_ASSOC), 'item_id');

$item_checkboxes = '';
foreach ($items as $item) {
    $checked = in_array((int) $item['id'], $selected_items) ? ' checked="checked"' : '';
    $item_checkboxes .= '<input type="checkbox" name="items[]" value="' . e($item['id']) . '"' . $checked . '> ' . e($item['name']) . '';
}

$runeStmt = db()->query('SELECT id, name FROM runes');
$runes = $runeStmt->fetchAll(PDO::FETCH_ASSOC);

$stmtRunes = db()->prepare('SELECT rune_id FROM guide_runes WHERE guide_id = ?');
$stmtRunes->execute([$guide_id]);
$selected_runes = array_column($stmtRunes->fetchAll(PDO::FETCH_ASSOC), 'rune_id');

$rune_checkboxes = '';
foreach ($runes as $rune) {
    $checked = in_array((int) $rune['id'], $selected_runes) ? ' checked="checked"' : '';
    $rune_checkboxes .= '<input type="checkbox" name="runes[]" value="' . e($rune['id']) . '"' . $checked . '> ' . e($rune['name']) . '';
}

render_page('templates/edit_guide.html', [
    'title' => 'Modifica guida - Rift Academy',
    'description' => 'Modifica una tua guida.',
], [
    'csrf' => $csrf,
    'errors' => errors_html($errors),
    'guide_id' => e($guide_id),
    'champion_options' => $champion_options,
    'role_options' => $role_options,
    'item_checkboxes' => $item_checkboxes,
    'rune_checkboxes' => $rune_checkboxes,
]);
