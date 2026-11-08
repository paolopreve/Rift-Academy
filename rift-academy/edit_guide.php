<?php
// edit_guide.php - Modifica di una guida esistente (autore o admin)
require_once 'includes/auth.php';
require_once 'includes/db.php';

require_login();

// L'id arriva sempre dalla query string (anche dopo l'invio del form)
$guide_id = get_int('id');

$stmt = db()->prepare('SELECT * FROM guides WHERE id = ?');
$stmt->execute([$guide_id]);
$guide = $stmt->fetch();

if (!$guide) {
    require '404.php';
    exit;
}
if (!is_owner_or_admin((int) $guide['user_id'])) {
    http_response_code(403);
    exit('Accesso negato.');
}

$errors = [];

$champions = db()->query('SELECT id, name FROM champions ORDER BY name')->fetchAll();
$roles = db()->query('SELECT id, name FROM roles ORDER BY id')->fetchAll();
$items = db()->query('SELECT id, name FROM items ORDER BY name')->fetchAll();
$runes = db()->query('SELECT id, name FROM runes ORDER BY name')->fetchAll();

// Valori iniziali: quelli salvati nel database
$title = $guide['title'];
$strategy_text = $guide['strategy_text'];
$patch = $guide['patch'];
$champion_id = (int) $guide['champion_id'];
$role_id = (int) $guide['role_id'];

$stmt = db()->prepare('SELECT item_id FROM guide_items WHERE guide_id = ? ORDER BY position');
$stmt->execute([$guide_id]);
$item_ids = array_map('intval', array_column($stmt->fetchAll(), 'item_id'));

$stmt = db()->prepare('SELECT rune_id FROM guide_runes WHERE guide_id = ? ORDER BY position');
$stmt->execute([$guide_id]);
$rune_ids = array_map('intval', array_column($stmt->fetchAll(), 'rune_id'));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $title = post_str('title');
    $strategy_text = post_str('strategy_text');
    $patch = post_str('patch');
    $champion_id = (int) post_str('champion_id');
    $role_id = (int) post_str('role_id');
    $item_ids = array_values(array_unique(array_map('intval', array_filter(post_arr('items'), 'is_numeric'))));
    $rune_ids = array_values(array_unique(array_map('intval', array_filter(post_arr('runes'), 'is_numeric'))));

    $validChampions = array_map('intval', array_column($champions, 'id'));
    $validRoles = array_map('intval', array_column($roles, 'id'));
    $validItems = array_map('intval', array_column($items, 'id'));
    $validRunes = array_map('intval', array_column($runes, 'id'));

    if (strlen($title) < 5 || strlen($title) > 120) {
        $errors[] = 'Il titolo deve avere tra 5 e 120 caratteri.';
    }
    if (strlen($strategy_text) < 20) {
        $errors[] = 'La strategia deve avere almeno 20 caratteri.';
    }
    if ($patch === '' || strlen($patch) > 10) {
        $errors[] = 'La patch è obbligatoria (massimo 10 caratteri, es. 14.10).';
    }
    if (!in_array($champion_id, $validChampions, true)) {
        $errors[] = 'Seleziona un campione dall\'elenco.';
    }
    if (!in_array($role_id, $validRoles, true)) {
        $errors[] = 'Seleziona un ruolo dall\'elenco.';
    }
    if (count(array_diff($item_ids, $validItems)) > 0) {
        $errors[] = 'Uno degli oggetti scelti non è valido.';
    }
    if (count(array_diff($rune_ids, $validRunes)) > 0) {
        $errors[] = 'Una delle rune scelte non è valida.';
    }

    if (empty($errors)) {
        $pdo = db();
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare('UPDATE guides SET title = ?, strategy_text = ?, patch = ?, champion_id = ?, role_id = ?, updated_at = NOW() WHERE id = ?');
            $stmt->execute([$title, $strategy_text, $patch, $champion_id, $role_id, $guide_id]);

            $stmt = $pdo->prepare('DELETE FROM guide_items WHERE guide_id = ?');
            $stmt->execute([$guide_id]);
            $stmt = $pdo->prepare('INSERT INTO guide_items (guide_id, item_id, position) VALUES (?, ?, ?)');
            foreach ($item_ids as $pos => $item_id) {
                $stmt->execute([$guide_id, $item_id, $pos + 1]);
            }

            $stmt = $pdo->prepare('DELETE FROM guide_runes WHERE guide_id = ?');
            $stmt->execute([$guide_id]);
            $stmt = $pdo->prepare('INSERT INTO guide_runes (guide_id, rune_id, position) VALUES (?, ?, ?)');
            foreach ($rune_ids as $pos => $rune_id) {
                $stmt->execute([$guide_id, $rune_id, $pos + 1]);
            }

            $pdo->commit();
        } catch (Exception $ex) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log($ex->getMessage());
            $errors[] = 'Errore durante l\'aggiornamento della guida. Riprova più tardi.';
        }

        if (empty($errors)) {
            flash_set('Guida aggiornata con successo.');
            redirect('guide_detail.php?id=' . $guide_id);
        }
    }
}

$champion_options = '<option value="">Seleziona un campione</option>';
foreach ($champions as $champion) {
    $sel = ((int) $champion['id'] === $champion_id) ? ' selected="selected"' : '';
    $champion_options .= '<option value="' . e($champion['id']) . '"' . $sel . '>' . e($champion['name']) . '</option>';
}

$role_options = '<option value="">Seleziona un ruolo</option>';
foreach ($roles as $role) {
    $sel = ((int) $role['id'] === $role_id) ? ' selected="selected"' : '';
    $role_options .= '<option value="' . e($role['id']) . '"' . $sel . '>' . e($role['name']) . '</option>';
}

$item_checkboxes = '';
foreach ($items as $item) {
    $chk = in_array((int) $item['id'], $item_ids, true) ? ' checked="checked"' : '';
    $item_checkboxes .= '<p><input id="item_' . e($item['id']) . '" type="checkbox" name="items[]" value="' . e($item['id']) . '"' . $chk . ' /> '
        . '<label class="inline-label" for="item_' . e($item['id']) . '">' . e($item['name']) . '</label></p>';
}

$rune_checkboxes = '';
foreach ($runes as $rune) {
    $chk = in_array((int) $rune['id'], $rune_ids, true) ? ' checked="checked"' : '';
    $rune_checkboxes .= '<p><input id="rune_' . e($rune['id']) . '" type="checkbox" name="runes[]" value="' . e($rune['id']) . '"' . $chk . ' /> '
        . '<label class="inline-label" for="rune_' . e($rune['id']) . '">' . e($rune['name']) . '</label></p>';
}

render_page('templates/edit_guide.html', [
    'title'       => 'Modifica guida - Rift Academy',
    'description' => 'Modifica una tua guida.',
], [
    'csrf'             => csrf_field(),
    'errors'           => errors_html($errors),
    'guide_id'         => e($guide_id),
    'title_value'      => e($title),
    'patch_value'      => e($patch),
    'strategy_value'   => e($strategy_text),
    'champion_options' => $champion_options,
    'role_options'     => $role_options,
    'item_checkboxes'  => $item_checkboxes,
    'rune_checkboxes'  => $rune_checkboxes,
]);
