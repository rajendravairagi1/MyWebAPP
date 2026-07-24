<?php
require_once __DIR__ . '/db.php';

function start_secure_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    $secure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_name('shivani_sess');
    session_start();

    // Basic idle timeout (30 min) + periodic session id rotation.
    $now = time();
    if (isset($_SESSION['last_activity']) && ($now - $_SESSION['last_activity']) > 1800) {
        session_unset();
        session_destroy();
        session_start();
    }
    $_SESSION['last_activity'] = $now;

    if (!isset($_SESSION['created_at'])) {
        $_SESSION['created_at'] = $now;
    } elseif ($now - $_SESSION['created_at'] > 900) {
        session_regenerate_id(true);
        $_SESSION['created_at'] = $now;
    }
}

function current_user(): ?array
{
    start_secure_session();
    if (empty($_SESSION['user_id'])) {
        return null;
    }
    return [
        'id' => $_SESSION['user_id'],
        'role' => $_SESSION['role'],
        'name' => $_SESSION['name'],
        'username' => $_SESSION['username'],
    ];
}

function is_super_admin(): bool
{
    $u = current_user();
    return $u && $u['role'] === 'super_admin';
}

function require_login(): array
{
    $u = current_user();
    if (!$u) {
        header('Location: ' . base_url('login.php'));
        exit;
    }
    return $u;
}

function require_role(string $role): array
{
    $u = require_login();
    if ($u['role'] !== $role) {
        http_response_code(403);
        echo 'Access denied.';
        exit;
    }
    return $u;
}

function attempt_login(string $username, string $password): bool
{
    $stmt = db()->prepare('SELECT * FROM users WHERE username = ? AND status = "active" LIMIT 1');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // Constant-time-ish: always run password_verify even on missing user
    // against a dummy hash, so login timing doesn't reveal valid usernames.
    $hash = $user['password_hash'] ?? '$2y$10$abcdefghijklmnopqrstuuvwxyzABCDEFGHIJKLMNOPQRSTUVWX1a';
    $ok = password_verify($password, $hash);

    if (!$user || !$ok) {
        log_activity(null, 'login_failed', 'username=' . $username);
        return false;
    }

    start_secure_session();
    session_regenerate_id(true);
    $_SESSION['user_id'] = (int)$user['id'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['name'] = $user['name'];
    $_SESSION['username'] = $user['username'];
    log_activity((int)$user['id'], 'login_success', null);
    return true;
}

function logout(): void
{
    start_secure_session();
    $u = current_user();
    if ($u) {
        log_activity($u['id'], 'logout', null);
    }
    $_SESSION = [];
    session_destroy();
}

function log_activity(?int $userId, string $action, ?string $details): void
{
    try {
        $stmt = db()->prepare('INSERT INTO activity_log (user_id, action, details, ip_address) VALUES (?,?,?,?)');
        $stmt->execute([$userId, $action, $details, $_SERVER['REMOTE_ADDR'] ?? null]);
    } catch (Throwable $e) {
        // Never let logging break the app.
    }
}
