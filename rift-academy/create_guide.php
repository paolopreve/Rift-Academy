<?php
// create_guide.php - Creazione di una nuova guida (utente registrato)
require_once 'includes/auth.php';
require_once 'includes/db.php';

require_login();

$champion_options = '';
$role_options = '';
$item_checkboxes = '';
$rune_checkboxes = '';
$errors = [];
$csrf_token = csrf_field();
$csrf_check = true;

try {
    // Caricare campioni, ruoli, oggetti e rune per i campi di selezione
    $stmt = db()->query('SELECT id, name FROM champions ORDER BY name');
    while ($champion = $stmt->fetch()) {
        $champion_options .= "<option value=\"{$champion['id']}\">{$champion['name']}</option>";
    }

    $stmt = db()->query('SELECT id, name FROM roles ORDER BY name');
    while ($role = $stmt->fetch()) {
        $role_options .= "<option value=\"{$role['id']}\">{$role['name']}</option>";
    }

    $stmt = db()->query('SELECT id, name FROM items ORDER BY name');
    while ($item = $stmt->fetch()) {
        $item_checkboxes .= "<input type=\"checkbox\" name=\"items[]\" value=\"{$item['id']}\"> {$item['name']}<br>";
    }

    $stmt = db()->query('SELECT id, name FROM runes ORDER BY name');
    while ($rune = $stmt->fetch()) {
        $rune_checkboxes .= "<input type=\"checkbox\" name=\"runes[]\" value=\"{$rune['id']}\"> {$rune['name']}<br>";
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        echo '<pre>';
        print_r($_POST);
        echo '</pre>';
        csrf_check();

        $title = post_str('title');
        $strategy_text = post_str('strategy_text');
        $patch = post_str('patch');
        $champion_id = get_int('champion_id');
        $role_id = get_int('role_id');
        $items = isset($_POST['items']) ? $_POST['items'] : [];
        $runes = isset($_POST['runes']) ? $_POST['runes'] : [];

        // Validazione dei campi
        if (empty($title)) {
            $errors[] = 'Il titolo è obbligatorio.';
        }
        if (empty($strategy_text)) {
            $errors[] = 'La strategia è obbligatoria.';
        }
        if (empty($patch)) {
            $errors[] = 'La patch è obbligatoria.';
        }
        if ($champion_id == 0) {
            $errors[] = 'Devi selezionare un campione.';
        }
        if ($role_id == 0) {
            $errors[] = 'Devi selezionare un ruolo.';
        }

        if (empty($errors)) {
            $pdo = db();
            $pdo->beginTransaction();

            try {
                $stmt = $pdo->prepare('INSERT INTO guides (user_id, champion_id, role_id, title, strategy_text, patch, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())');
                $stmt->execute([current_user()['id'], $champion_id, $role_id, $title, $strategy_text, $patch]);

                $guide_id = $pdo->lastInsertId();

                // Inserimento degli oggetti
                if (!empty($items)) {
                    $stmt = $pdo->prepare('INSERT INTO guide_items (guide_id, item_id) VALUES (?, ?)');
                    foreach ($items as $item_id) {
                        $stmt->execute([$guide_id, $item_id]);
                    }
                }

                // Inserimento delle rune
                if (!empty($runes)) {
                    $stmt = $pdo->prepare('INSERT INTO guide_runes (guide_id, rune_id) VALUES (?, ?)');
                    foreach ($runes as $rune_id) {
                        $stmt->execute([$guide_id, $rune_id]);
                    }
                }

                $pdo->commit();

                flash_set('Guida creata con successo.');
                redirect("guide_detail.php?id=$guide_id");
            } catch (Exception $e) {
                $pdo->rollBack();
                error_log($e->getMessage());
                $errors[] = 'Errore durante la creazione della guida. Riprova più tardi.';
            }
        }
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    $errors[] = 'Errore nel caricamento dei dati. Riprova più tardi.';
}

render_page('templates/create_guide.html', [
    'title'       => 'Nuova guida - Rift Academy',
    'description' => 'Pubblica una nuova guida o build.',
], [
    'csrf' => $csrf_token,
    'errors' => errors_html($errors),
    'champion_options' => $champion_options,
    'role_options' => $role_options,
    'item_checkboxes' => $item_checkboxes,
    'rune_checkboxes' => $rune_checkboxes,
]);