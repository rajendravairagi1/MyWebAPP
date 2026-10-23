<?php
declare(strict_types=1);

const RESET_MINUTES = 15;
const RESET_MAX_TRIES = 5;

function ensure_reset_table(): void {
    static $done = false;
    if ($done) return;
    db()->exec('CREATE TABLE IF NOT EXISTS password_resets (
        user_id CHAR(36) NOT NULL PRIMARY KEY, code_hash CHAR(64) NOT NULL,
        expires_at DATETIME NOT NULL, tries INT NOT NULL DEFAULT 0, created_at DATETIME NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');
    $done = true;
}

function find_user_by_identifier(string $ident): ?array {
    $e = strtolower(trim($ident));
    $m = preg_replace('/\D+/', '', $ident) ?? '';
    $st = db()->prepare('SELECT id, name, email, is_active FROM users WHERE email = ? OR (mobile IS NOT NULL AND mobile = ?) LIMIT 1');
    $st->execute([$e, $m]);
    $u = $st->fetch();
    return $u ?: null;
}

/** POST /v1/auth/forgot  { identifier } — always answers ok so nobody can probe which accounts exist. */
function forgot_password(): array {
    ensure_reset_table();
    $ident = str_field(body(), 'identifier', 190);
    $key = strtolower($ident);
    rate_limit('forgot', client_ip(), 10, 3600);
    rate_limit('forgot', $key, 3, 3600);
    note_attempt('forgot', client_ip());
    note_attempt('forgot', $key);

    $u = find_user_by_identifier($ident);
    if ($u && (int) $u['is_active'] && !empty($u['email'])) {
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        db()->prepare('REPLACE INTO password_resets (user_id, code_hash, expires_at, tries, created_at) VALUES (?, ?, ?, 0, ?)')
            ->execute([$u['id'], hash('sha256', $code . $u['id']), gmdate('Y-m-d H:i:s', time() + RESET_MINUTES * 60), now()]);
        $sent = send_mail(
            $u['email'],
            'HazriBook password reset code',
            "Hello {$u['name']},\n\nYour HazriBook password reset code is: $code\n\n"
            . 'It works for ' . RESET_MINUTES . " minutes. If you did not ask for this, ignore this email.\n\nHazriBook"
        );
        if (!$sent) error_log('HazriBook: reset email could not be sent to a user');
    }
    return ['message' => 'If an account with an email exists, a code has been sent to it.'];
}

/** POST /v1/auth/reset  { identifier, code, password } */
function reset_password(): array {
    ensure_reset_table();
    $b = body();
    $ident = str_field($b, 'identifier', 190);
    $code = preg_replace('/\D+/', '', (string) ($b['code'] ?? '')) ?? '';
    $password = (string) ($b['password'] ?? '');
    check_password_strength($password);
    rate_limit('reset', client_ip(), 20, 3600);
    note_attempt('reset', client_ip());

    $bad = new ApiError(400, 'The code is wrong or has expired. Ask for a new one.', 'bad_code');
    $u = find_user_by_identifier($ident);
    if (!$u) throw $bad;
    $st = db()->prepare('SELECT code_hash, expires_at, tries FROM password_resets WHERE user_id = ?');
    $st->execute([$u['id']]);
    $r = $st->fetch();
    if (!$r || $r['expires_at'] < now() || (int) $r['tries'] >= RESET_MAX_TRIES) throw $bad;
    if (!hash_equals($r['code_hash'], hash('sha256', $code . $u['id']))) {
        db()->prepare('UPDATE password_resets SET tries = tries + 1 WHERE user_id = ?')->execute([$u['id']]);
        throw $bad;
    }
    $pdo = db();
    $pdo->beginTransaction();
    $pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ?')->execute([password_hash($password, PASSWORD_DEFAULT), $u['id']]);
    $pdo->prepare('DELETE FROM tokens WHERE user_id = ?')->execute([$u['id']]);
    $pdo->prepare('DELETE FROM password_resets WHERE user_id = ?')->execute([$u['id']]);
    $pdo->commit();
    return [];
}
