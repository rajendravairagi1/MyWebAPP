<?php
require_once __DIR__ . '/../db.php';

function isHttpsRequest(): bool
{
    if (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
        return true;
    }
    if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https') {
        return true;
    }
    return !empty($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443;
}

function startSecureSession(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    // Auto-detect HTTPS so the session cookie isn't marked "secure" on a
    // plain-HTTP site (a secure cookie is silently dropped by the browser
    // over HTTP, which breaks login/CSRF with "Invalid CSRF token").
    $secureCookie = isHttpsRequest();
    if (defined('APP_FORCE_HTTPS_COOKIE') && APP_FORCE_HTTPS_COOKIE === false) {
        $secureCookie = false;
    }
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax',
        'secure' => $secureCookie,
    ]);
    session_start();

    // Some shared hosts (LiteSpeed/cPanel, Cloudflare, etc.) cache full HTML
    // pages by default. A cached login page keeps serving an old CSRF token
    // forever, so every submit fails with "Invalid CSRF token". These
    // headers stop that page from being cached anywhere.
    if (!headers_sent()) {
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
    }
}

function currentUser(): ?array
{
    startSecureSession();
    if (!empty($_SESSION['user_id'])) {
        return ['id' => $_SESSION['user_id'], 'username' => $_SESSION['username']];
    }
    return null;
}

function requireLogin(): void
{
    if (!currentUser()) {
        header('Location: login.php');
        exit;
    }
}

function attemptLogin(string $username, string $password): bool
{
    $pdo = getDb();
    $stmt = $pdo->prepare('SELECT id, username, password_hash FROM users WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        usleep(300000); // small delay against brute-force / timing attacks
        return false;
    }

    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    return true;
}

function logoutUser(): void
{
    startSecureSession();
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}

// ---------- CSRF ----------
function csrfToken(): string
{
    startSecureSession();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrfField(): string
{
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrfToken()) . '">';
}

function verifyCsrf(): void
{
    startSecureSession();
    $token = $_POST['csrf_token'] ?? $_GET['csrf_token'] ?? '';
    if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        http_response_code(403);
        die('Invalid CSRF token. Please refresh the page and try again.');
    }
}
