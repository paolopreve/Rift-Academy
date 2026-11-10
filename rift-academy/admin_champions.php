<?php
// admin_champions.php - Inserimento e aggiornamento di campioni (admin)
require_once 'includes/auth.php';
require_once 'includes/db.php';

require_admin();

$errors = [];
$roles = db()->query('SELECT id, name FROM roles ORDER BY id')->fetchAll();
$validRoles = array_map('intval', array_column($roles, 'id'));

// Valori del form (vuoti = nuovo campione)
$editId = get_int('edit');
$name = '';
$image_path = '';
$role_ids = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $editId = (int) post_str('champion_id');
    $name = post_str('name');
    $image_path = post_str('image_path');
    $role_ids = array_values(array_unique(array_map('intval', array_filter(post_arr('roles'), 'is_numeric'))));

    if ($name === '' || strlen($name) > 50) {
        $errors[] = 'Il nome è obbligatorio (massimo 50 caratteri).';
    }
    // Percorso relativo, senza "..", con soli caratteri semplici
    if ($image_path === '' || strlen($image_path) > 255 || !preg_match('/^[A-Za-z0-9_.\/-]+$/', $image_path)
        || $image_path[0] === '/' || strpos($image_path, '..') !== false) {
        $errors[] = 'Il percorso immagine deve essere relativo, ad esempio img/champions/Ahri.png.';
    }
    if (count(array_diff($role_ids, $validRoles)) > 0) {
        $errors[] = 'Uno dei ruoli scelti non è valido.';
    }
    if ($editId > 0) {
        $stmt = db()->prepare('SELECT id FROM champions WHERE id = ?');
        $stmt->execute([$editId]);
        if (!$stmt->fetch()) {
            $errors[] = 'Il campione da modificare non esiste.';
        }
    }

    if (empty($errors)) {
        $pdo = db();
        try {
            $pdo->beginTransaction();

            if ($editId > 0) {
                $stmt = $pdo->prepare('UPDATE champions SET name = ?, image_path = ? WHERE id = ?');
                $stmt->execute([$name, $image_path, $editId]);
                $championId = $editId;
                $stmt = $pdo->prepare('DELETE FROM champion_roles WHERE champion_id = ?');
                $stmt->execute([$championId]);
            } else {
                $stmt = $pdo->prepare('INSERT INTO champions (name, image_path) VALUES (?, ?)');
                $stmt->execute([$name, $image_path]);
                $championId = (int) $pdo->lastInsertId();
            }

            $stmt = $pdo->prepare('INSERT INTO champion_roles (champion_id, role_id) VALUES (?, ?)');
            foreach ($role_ids as $roleId) {
                $stmt->execute([$championId, $roleId]);
            }

            $pdo->commit();
        } catch (Exception $ex) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log($ex->getMessage());
            $errors[] = 'Salvataggio non riuscito: controlla che il nome non esista già.';
        }

        if (empty($errors)) {
            flash_set($editId > 0 ? 'Campione aggiornato.' : 'Campione aggiunto.');
            redirect('admin_champions.php');
        }
    }
} elseif ($editId > 0) {
    // Precompila il form con il campione da modificare
    $stmt = db()->prepare('SELECT name, image_path FROM champions WHERE id = ?');
    $stmt->execute([$editId]);
    $row = $stmt->fetch();
    if ($row) {
        $name = $row['name'];
        $image_path = $row['image_path'];
        $stmt = db()->prepare('SELECT role_id FROM champion_roles WHERE champion_id = ?');
        $stmt->execute([$editId]);
        $role_ids = array_map('intval', array_column($stmt->fetchAll(), 'role_id'));
    } else {
        $editId = 0;
    }
}

$roleCheckboxes = '';
foreach ($roles as $role) {
    $chk = in_array((int) $role['id'], $role_ids, true) ? ' checked="checked"' : '';
    $roleCheckboxes .= '<p><input id="role_' . e($role['id']) . '" type="checkbox" name="roles[]" value="' . e($role['id']) . '"' . $chk . ' /> '
        . '<label class="inline-label" for="role_' . e($role['id']) . '" lang="en">' . e($role['name']) . '</label></p>';
}

$champions = db()->query('SELECT c.id, c.name, c.image_path, GROUP_CONCAT(r.name ORDER BY r.id SEPARATOR \', \') AS role_names
                          FROM champions c
                          LEFT JOIN champion_roles cr ON cr.champion_id = c.id
                          LEFT JOIN roles r ON r.id = cr.role_id
                          GROUP BY c.id, c.name, c.image_path ORDER BY c.name')->fetchAll();

$table = '<table><caption>Campioni nel catalogo</caption><thead><tr>'
    . '<th scope="col">Nome</th><th scope="col">Immagine</th><th scope="col">Ruoli</th><th scope="col">Azioni</th>'
    . '</tr></thead><tbody>';
foreach ($champions as $champion) {
    $table .= '<tr><th scope="row">' . e($champion['name']) . '</th><td>' . e($champion['image_path']) . '</td><td>'
        . en(e($champion['role_names'] !== null ? $champion['role_names'] : 'non assegnati')) . '</td>'
        . '<td><a href="admin_champions.php?edit=' . e($champion['id']) . '">Modifica ' . e($champion['name']) . '</a></td></tr>';
}
$table .= '</tbody></table>';

render_page('templates/admin_champions.html', [
    'title'       => 'Catalogo campioni - Rift Academy',
    'description' => 'Gestione del catalogo di campioni.',
], [
    'csrf'              => csrf_field(),
    'errors'            => errors_html($errors),
    'champion_id_field' => '<input type="hidden" name="champion_id" value="' . e($editId) . '" />',
    'name_value'        => e($name),
    'image_value'       => e($image_path),
    'role_checkboxes'   => $roleCheckboxes,
    'champions_table'   => $table,
]);
