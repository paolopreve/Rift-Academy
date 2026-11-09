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
        return '<ul class="user-nav"><li><a href="login.php">Accedi</a></li><li><a class="btn" href="register.php">Registrati</a></li></ul>';
    }
    $html = '<ul class="user-nav"><li><a href="profile.php">Profilo</a></li>';
    if ($u['role'] === 'admin') {
        $html .= '<li><a href="admin_dashboard.php">Amministrazione</a></li>';
    }
    $html .= '<li><a class="btn" href="create_guide.php">Nuova guida</a></li>';
    $html .= '<li><form action="logout.php" method="post">' . csrf_field() . '<button type="submit">Esci</button></form></li></ul>';
    return $html;
}

// Versione leggera di un'immagine (es. images/champions/ahri.jpg -> ahri-small.webp).
// Se la versione ridotta non esiste si usa l'originale.
function img_small(string $path): string
{
    $small = preg_replace('/\.(jpe?g|png)$/i', '-small.webp', $path);
    if ($small !== null && $small !== $path && is_file(__DIR__ . '/../' . $small)) {
        return $small;
    }
    return $path;
}

// Data in formato breve italiano, es. "9 nov 2026" (da una data MySQL).
function data_breve(string $mysqlDate): string
{
    $mesi = ['gen', 'feb', 'mar', 'apr', 'mag', 'giu', 'lug', 'ago', 'set', 'ott', 'nov', 'dic'];
    $ts = strtotime($mysqlDate);
    if ($ts === false) {
        return $mysqlDate;
    }
    return date('j', $ts) . ' ' . $mesi[(int) date('n', $ts) - 1] . ' ' . date('Y', $ts);
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
        '{{description}}' => e(!empty($meta['description']) ? $meta['description'] : 'Rift Academy: guide, build e rune per League of Legends scritte dalla community.'),
        '{{keywords}}'    => e(!empty($meta['keywords']) ? $meta['keywords'] : 'League of Legends, guide, build, rune, campioni, Top, Jungle, Mid, Bot, Support'),
        '{{nav_user}}'    => nav_user_html(),
        '{{flash}}'       => flash_html(),
    ]);

    // Segna la voce di menu della pagina corrente (aria-current, letto dagli screen reader)
    $current = basename($_SERVER['SCRIPT_NAME']);
    $header = str_replace('<li><a href="' . $current . '">', '<li><a href="' . $current . '" aria-current="page">', $header);

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
