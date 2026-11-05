<?php
// Sessione, autenticazione, ruoli e protezione CSRF.
// Richiede PHP 7.3 o superiore (verificare la versione sul server del corso).
require_once __DIR__ . '/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'httponly' => true,
        'samesite' => 'Lax',
        'secure'   => !empty($_SERVER['HTTPS']),
    ]);
    session_start();
}

// NOTA: lo status 'suspended' viene controllato al login. Per rendere effettiva
// subito la sospensione di un utente gia' connesso, rileggere lo status dal DB
// ad ogni richiesta (o a intervalli) e fare logout_user() se non e' 'active'.
function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function login_user(array $row): void
{
    session_regenerate_id(true);
    $_SESSION['user'] = [
        'id'       => (int) $row['id'],
        'username' => $row['username'],
        'role'     => $row['role'],
    ];
    unset($_SESSION['csrf']);
}

function logout_user(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 3600, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}

function require_login(): void
{
    if (current_user() === null) {
        redirect('login.php');
    }
}

function require_admin(): void
{
    $u = current_user();
    if ($u === null) {
        redirect('login.php');
    }
    if ($u['role'] !== 'admin') {
        http_response_code(403);
        exit('Accesso negato.');
    }
}

// Controllo lato server: l'utente deve essere l'autore oppure admin.
// Nascondere un pulsante nell'interfaccia NON basta.
function is_owner_or_admin(int $ownerId): bool
{
    $u = current_user();
    return $u !== null && ($u['role'] === 'admin' || $u['id'] === $ownerId);
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '" />';
}

function csrf_check(): void
{
    $sent = $_POST['csrf'] ?? '';
    if (!is_string($sent) || !hash_equals(csrf_token(), $sent)) {
        http_response_code(400);
        exit('Richiesta non valida (token di sicurezza mancante o scaduto).');
    }
}
