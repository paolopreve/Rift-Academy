<?php
// Funzioni comuni: escape, redirect, messaggi, rendering dei template.
// Questo file non dipende da auth.php al momento del caricamento; render_page()
// usa current_user() e csrf_field(), quindi le pagine caricano includes/auth.php.

// Escape per l'OUTPUT di qualsiasi dato inserito dagli utenti (anti XSS).
function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function require_post(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect('index.php');
    }
}

function get_string(string $key): string
{
    return isset($_GET[$key]) ? trim($_GET[$key]) : '';
}

function post_str(string $key): string
{
    return isset($_POST[$key]) && is_string($_POST[$key]) ? trim($_POST[$key]) : '';
}

function post_arr(string $key): array
{
 return isset($_POST[$key]) && is_array($_POST[$key]) ? $_POST[$key] : [];
}

function get_int(string $key): int
{
    return isset($_GET[$key]) ? (int) $_GET[$key] : 0;
}

function flash_set(string $message): void
{
    $_SESSION['flash'] = $message;
}

function flash_html(): string
{
    if (empty($_SESSION['flash'])) {
        return '';
    }
    $msg = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return '<div class="flash" role="status"><p>' . e($msg) . '</p></div>';
}

function errors_html(array $errors): string
{
    if (!$errors) {
        return '';
    }
    $items = '';
    foreach ($errors as $err) {
        $items .= '<li>' . e($err) . '</li>';
    }
    return '<div class="form-errors" role="alert"><p>Controlla i dati inseriti:</p><ul>' . $items . '</ul></div>';
}

function nav_user_html(): string
{
    $u = current_user();
    if ($u === null) {
        return '<ul class="user-nav"><li><a href="login.php">Accedi</a></li><li><a href="register.php">Registrati</a></li></ul>';
    }
    $html = '<ul class="user-nav"><li><a href="create_guide.php">Nuova guida</a></li><li><a href="profile.php">Profilo</a></li>';
    if ($u['role'] === 'admin') {
        $html .= '<li><a href="admin_dashboard.php">Amministrazione</a></li>';
    }
    $html .= '<li><form action="logout.php" method="post">' . csrf_field() . '<button type="submit">Esci</button></form></li></ul>';
    return $html;
}

// Compone header + pagina + footer.
// $meta: title, description (vengono escapati qui).
// $vars: segnaposto {{nome}} => HTML GIA' ESCAPATO dal chiamante (usare e()).
function render_page(string $template, array $meta, array $vars = []): void
{
    $base = __DIR__ . '/../';
    $header = file_get_contents($base . 'templates/header.html');
    $footer = file_get_contents($base . 'templates/footer.html');
    $page = file_get_contents($base . $template);

    $header = strtr($header, [
        '{{title}}'       => e($meta['title'] ?? 'Rift Academy'),
        '{{description}}' => e($meta['description'] ?? ''),
        '{{nav_user}}'    => nav_user_html(),
        '{{flash}}'       => flash_html(),
    ]);

    $repl = [];
    foreach ($vars as $key => $html) {
        $repl['{{' . $key . '}}'] = $html;
    }
    $page = strtr($page, $repl);

    // Rimuove eventuali segnaposto non valorizzati.
    $out = preg_replace('/\{\{[a-z_]+\}\}/', '', $header . $page . $footer);

    header('Content-Type: text/html; charset=utf-8');
    echo $out;
}
