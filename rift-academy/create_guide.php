<?php
// create_guide.php - Creazione di una nuova guida (utente registrato)
require_once 'includes/auth.php';
require_once 'includes/db.php';

require_login();

$errors = [];

// Dati per i campi di selezione (letti sempre, servono anche per validare)
$champions = db()->query('SELECT id, name FROM champions ORDER BY name')->fetchAll();
$roles = db()->query('SELECT id, name FROM roles ORDER BY id')->fetchAll();
$items = db()->query('SELECT id, name FROM items ORDER BY name')->fetchAll();
$runes = db()->query('SELECT id, name FROM runes ORDER BY name')->fetchAll();

// Valori del form (vuoti alla prima visita, ripopolati dopo un errore)
$title = '';
$strategy_text = '';
$patch = '';
$champion_id = 0;
$role_id = 0;
$item_ids = [];
$rune_ids = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $title = post_str('title');
    $strategy_text = post_str('strategy_text');
    $patch = post_str('patch');
    $champion_id = (int) post_str('champion_id');
    $role_id = (int) post_str('role_id');
    $item_ids = array_values(array_unique(array_map('intval', array_filter(post_arr('items'), 'is_numeric'))));
    $rune_ids = array_values(array_unique(array_map('intval', array_filter(post_arr('runes'), 'is_numeric'))));

    // Elenchi degli id esistenti per controllare i valori ricevuti
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

            $stmt = $pdo->prepare('INSERT INTO guides (user_id, champion_id, role_id, title, strategy_text, patch) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->execute([current_user()['id'], $champion_id, $role_id, $title, $strategy_text, $patch]);
            $guide_id = (int) $pdo->lastInsertId();

            $stmt = $pdo->prepare('INSERT INTO guide_items (guide_id, item_id, position) VALUES (?, ?, ?)');
            foreach ($item_ids as $pos => $item_id) {
                $stmt->execute([$guide_id, $item_id, $pos + 1]);
            }

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
            $errors[] = 'Errore durante la creazione della guida. Riprova più tardi.';
        }

        if (empty($errors)) {
            flash_set('Guida creata con successo.');
            redirect('guide_detail.php?id=' . $guide_id);
        }
    }
}

// HTML dei campi di selezione, con i valori correnti
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

render_page('templates/create_guide.html', [
    'title'       => 'Nuova guida - Rift Academy',
    'description' => 'Pubblica una nuova guida o build.',
], [
    'csrf'             => csrf_field(),
    'errors'           => errors_html($errors),
    'title_value'      => e($title),
    'patch_value'      => e($patch),
    'strategy_value'   => e($strategy_text),
    'champion_options' => $champion_options,
    'role_options'     => $role_options,
    'item_checkboxes'  => $item_checkboxes,
    'rune_checkboxes'  => $rune_checkboxes,
]);
