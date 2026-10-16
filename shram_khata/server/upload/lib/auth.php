<?php
declare(strict_types=1);

const TOKEN_DAYS = 90;

/** Count recent attempts for a key and refuse when over the limit. */
function rate_limit(string $kind, string $key, int $max, int $windowSeconds): void {
    $k = hash('sha256', $key);
    $since = gmdate('Y-m-d H:i:s', time() - $windowSeconds);
    $st = db()->prepare('SELECT COUNT(*) FROM attempts WHERE kind = ? AND k = ? AND at >= ?');
    $st->execute([$kind, $k, $since]);
    if ((int) $st->fetchColumn() >= $max) {
        throw new ApiError(429, 'Too many attempts. Please try again later.', 'rate_limited');
    }
}

function note_attempt(string $kind, string $key): void {
    $st = db()->prepare('INSERT INTO attempts (kind, k, at) VALUES (?, ?, ?)');
    $st->execute([$kind, hash('sha256', $key), now()]);
    if (random_int(1, 50) === 1) {
        db()->prepare('DELETE FROM attempts WHERE at < ?')->execute([gmdate('Y-m-d H:i:s', time() - 86400)]);
    }
}

function issue_token(string $userId, string $agencyId, string $device): string {
    $token = bin2hex(random_bytes(32));
    $st = db()->prepare('INSERT INTO tokens (token_hash, user_id, agency_id, device_name, created_at, last_used, expires_at)
                         VALUES (?, ?, ?, ?, ?, ?, ?)');
    $st->execute([
        hash('sha256', $token), $userId, $agencyId, mb_substr($device, 0, 100),
        now(), now(), gmdate('Y-m-d H:i:s', time() + TOKEN_DAYS * 86400),
    ]);
    return $token;
}

function bearer_token(): ?string {
    $h = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
    if ($h === '' && function_exists('getallheaders')) {
        foreach (getallheaders() as $k => $v) {
            if (strtolower($k) === 'authorization') $h = $v;
        }
    }
    if (preg_match('/^Bearer\s+([a-f0-9]{64})$/i', $h, $m)) return strtolower($m[1]);
    return null;
}

/** The signed-in user (with agency) or a 401. */
function require_user(): array {
    $t = bearer_token();
    if ($t === null) throw new ApiError(401, 'Please sign in.', 'unauthorized');
    $st = db()->prepare(
        'SELECT u.id, u.agency_id, u.name, u.email, u.mobile, u.kind, u.scope_company_id, u.is_active,
                a.is_active AS agency_active, a.plan_id, a.name AS agency_name, k.expires_at
           FROM tokens k
           JOIN users u ON u.id = k.user_id
           JOIN agencies a ON a.id = u.agency_id
          WHERE k.token_hash = ?'
    );
    $st->execute([hash('sha256', $t)]);
    $u = $st->fetch();
    if (!$u || $u['expires_at'] < now() || !(int) $u['is_active'] || !(int) $u['agency_active']) {
        throw new ApiError(401, 'Your session has ended. Please sign in again.', 'unauthorized');
    }
    // Keep the session alive while it is used (at most once a minute).
    db()->prepare('UPDATE tokens SET last_used = ?, expires_at = ? WHERE token_hash = ? AND last_used < ?')
        ->execute([now(), gmdate('Y-m-d H:i:s', time() + TOKEN_DAYS * 86400), hash('sha256', $t), gmdate('Y-m-d H:i:s', time() - 60)]);
    return $u;
}

function require_owner(array $u): void {
    if ($u['kind'] !== 'owner') throw new ApiError(403, 'Only the owner can do this.', 'forbidden');
}

function user_json(array $u): array {
    return [
        'id' => $u['id'],
        'agencyId' => $u['agency_id'],
        'agencyName' => $u['agency_name'] ?? null,
        'planId' => $u['plan_id'] ?? null,
        'name' => $u['name'],
        'email' => $u['email'],
        'mobile' => $u['mobile'],
        'kind' => $u['kind'],
        'scopeCompanyId' => $u['scope_company_id'],
    ];
}

function check_password_strength(string $p): void {
    if (strlen($p) < 8) throw new ApiError(422, 'Password must be at least 8 characters.', 'weak_password');
    if (strlen($p) > 200) throw new ApiError(422, 'Password is too long.', 'invalid');
}
