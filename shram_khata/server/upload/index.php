<?php
declare(strict_types=1);

require __DIR__ . '/lib/util.php';
require __DIR__ . '/lib/db.php';
require __DIR__ . '/lib/auth.php';
require __DIR__ . '/lib/sync.php';

const API_VERSION = '1.0.0';

set_exception_handler(function (Throwable $e): void {
    if ($e instanceof ApiError) {
        json_out($e->status, ['ok' => false, 'error' => ['code' => $e->errCode, 'message' => $e->getMessage()]]);
    }
    error_log('HazriBook error: ' . $e);
    $debug = false;
    try { $debug = (bool) cfg('debug', false); } catch (Throwable) {}
    json_out(500, ['ok' => false, 'error' => [
        'code' => 'server_error',
        'message' => $debug ? $e->getMessage() : 'Something went wrong on the server.',
    ]]);
});

try { date_default_timezone_set((string) cfg('timezone', 'UTC')); } catch (ApiError) {}

// HTTPS only (except for local testing).
$host = $_SERVER['HTTP_HOST'] ?? '';
$https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
if (!$https && !preg_match('/^(localhost|127\.0\.0\.1)(:\d+)?$/', $host)) {
    json_out(400, ['ok' => false, 'error' => ['code' => 'https_required', 'message' => 'Use https.']]);
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
if ($base !== '' && str_starts_with($path, $base)) $path = substr($path, strlen($base));
$path = '/' . trim($path, '/');

function ok(array $data = []): never { json_out(200, ['ok' => true] + $data); }

// ---- public ---------------------------------------------------------------

if ($path === '/' || $path === '/v1/health') {
    $dbOk = true;
    try { db()->query('SELECT 1'); } catch (Throwable) { $dbOk = false; }
    ok(['service' => 'hazribook', 'version' => API_VERSION, 'time' => gmdate('c'), 'db' => $dbOk]);
}

if ($path === '/v1/auth/register' && $method === 'POST') {
    if (!cfg('allow_register', true)) throw new ApiError(403, 'Sign-up is closed.', 'register_closed');
    rate_limit('register', client_ip(), 5, 3600);
    note_attempt('register', client_ip());

    $b = body();
    $business = str_field($b, 'businessName', 200);
    $owner = str_field($b, 'ownerName', 150);
    $email = normalize_email(str_field($b, 'email', 190, false));
    $mobile = normalize_mobile(str_field($b, 'mobile', 20, false));
    if ($email === null && $mobile === null) throw new ApiError(422, 'Enter an email or a mobile number.', 'invalid');
    $password = (string) ($b['password'] ?? '');
    check_password_strength($password);
    $plan = in_array($b['planId'] ?? '', ['solo', 'team', 'company'], true) ? $b['planId'] : 'solo';

    $pdo = db();
    $dup = $pdo->prepare('SELECT 1 FROM users WHERE (email IS NOT NULL AND email = ?) OR (mobile IS NOT NULL AND mobile = ?) LIMIT 1');
    $dup->execute([$email, $mobile]);
    if ($dup->fetchColumn()) throw new ApiError(409, 'An account with this email or mobile already exists.', 'exists');

    $agencyId = uuid4();
    $userId = uuid4();
    $pdo->beginTransaction();
    try {
        $pdo->prepare('INSERT INTO agencies (id, name, plan_id, seq, is_active, created_at) VALUES (?, ?, ?, 0, 1, ?)')
            ->execute([$agencyId, $business, $plan, now()]);
        $pdo->prepare('INSERT INTO users (id, agency_id, name, email, mobile, password_hash, kind, is_active, created_at)
                       VALUES (?, ?, ?, ?, ?, ?, ?, 1, ?)')
            ->execute([$userId, $agencyId, $owner, $email, $mobile, password_hash($password, PASSWORD_DEFAULT), 'owner', now()]);
        $pdo->commit();
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        if ($e->getCode() === '23000') throw new ApiError(409, 'An account with this email or mobile already exists.', 'exists');
        throw $e;
    }
    $token = issue_token($userId, $agencyId, (string) ($b['device'] ?? ''));
    $u = ['id' => $userId, 'agency_id' => $agencyId, 'agency_name' => $business, 'plan_id' => $plan, 'name' => $owner,
          'email' => $email, 'mobile' => $mobile, 'kind' => 'owner', 'scope_company_id' => null];
    ok(['token' => $token, 'user' => user_json($u)]);
}

if ($path === '/v1/auth/login' && $method === 'POST') {
    $b = body();
    $ident = str_field($b, 'identifier', 190);
    $password = (string) ($b['password'] ?? '');
    $key = str_contains($ident, '@') ? strtolower($ident) : (preg_replace('/\D+/', '', $ident) ?? $ident);
    rate_limit('login', $key, 8, 900);
    rate_limit('login', 'ip:' . client_ip(), 40, 900);

    $st = db()->prepare('SELECT u.*, a.name AS agency_name, a.plan_id, a.is_active AS agency_active
                           FROM users u JOIN agencies a ON a.id = u.agency_id
                          WHERE u.email = ? OR u.mobile = ? LIMIT 1');
    $st->execute([strtolower($ident), $key]);
    $u = $st->fetch();
    // Always run one hash check so unknown accounts take as long as known ones.
    $hash = $u['password_hash'] ?? '$2y$10$abcdefghijklmnopqrstuuJ8m0kqkz0h0kq0Zr1x2y3z4A5B6C7D8E';
    $valid = password_verify($password, $hash) && $u && (int) $u['is_active'] && (int) $u['agency_active'];
    if (!$valid) {
        note_attempt('login', $key);
        note_attempt('login', 'ip:' . client_ip());
        throw new ApiError(401, 'Wrong email / mobile or password.', 'bad_credentials');
    }
    if (password_needs_rehash($u['password_hash'], PASSWORD_DEFAULT)) {
        db()->prepare('UPDATE users SET password_hash = ? WHERE id = ?')
            ->execute([password_hash($password, PASSWORD_DEFAULT), $u['id']]);
    }
    db()->prepare('UPDATE users SET last_login = ? WHERE id = ?')->execute([now(), $u['id']]);
    $token = issue_token($u['id'], $u['agency_id'], (string) ($b['device'] ?? ''));
    ok(['token' => $token, 'user' => user_json($u)]);
}

// ---- signed in --------------------------------------------------------------

$user = require_user();

if ($path === '/v1/me' && $method === 'GET') ok(['user' => user_json($user)]);

if ($path === '/v1/auth/logout' && $method === 'POST') {
    db()->prepare('DELETE FROM tokens WHERE token_hash = ?')->execute([hash('sha256', (string) bearer_token())]);
    ok();
}

if ($path === '/v1/auth/password' && $method === 'POST') {
    $b = body();
    $st = db()->prepare('SELECT password_hash FROM users WHERE id = ?');
    $st->execute([$user['id']]);
    if (!password_verify((string) ($b['current'] ?? ''), (string) $st->fetchColumn())) {
        throw new ApiError(403, 'Current password is wrong.', 'bad_credentials');
    }
    $new = (string) ($b['new'] ?? '');
    check_password_strength($new);
    db()->prepare('UPDATE users SET password_hash = ? WHERE id = ?')->execute([password_hash($new, PASSWORD_DEFAULT), $user['id']]);
    // Sign out every other device.
    db()->prepare('DELETE FROM tokens WHERE user_id = ? AND token_hash <> ?')
        ->execute([$user['id'], hash('sha256', (string) bearer_token())]);
    ok();
}

if ($path === '/v1/sync/push' && $method === 'POST') ok(sync_push($user));
if ($path === '/v1/sync/pull' && $method === 'GET') ok(sync_pull($user));

// ---- people (owner only) ------------------------------------------------------

if ($path === '/v1/users' && $method === 'GET') {
    require_owner($user);
    $st = db()->prepare('SELECT id, name, email, mobile, kind, scope_company_id, is_active, last_login
                           FROM users WHERE agency_id = ? ORDER BY created_at');
    $st->execute([$user['agency_id']]);
    ok(['users' => array_map(fn($r) => [
        'id' => $r['id'], 'name' => $r['name'], 'email' => $r['email'], 'mobile' => $r['mobile'],
        'kind' => $r['kind'], 'scopeCompanyId' => $r['scope_company_id'],
        'isActive' => (bool) $r['is_active'], 'lastLogin' => $r['last_login'],
    ], $st->fetchAll())]);
}

if ($path === '/v1/users' && $method === 'POST') {
    require_owner($user);
    $b = body();
    $kind = $b['kind'] ?? 'staff';
    if (!in_array($kind, ['staff', 'manager'], true)) throw new ApiError(422, 'kind must be staff or manager.', 'invalid');
    $name = str_field($b, 'name', 150);
    $email = normalize_email(str_field($b, 'email', 190, false));
    $mobile = normalize_mobile(str_field($b, 'mobile', 20, false));
    if ($email === null && $mobile === null) throw new ApiError(422, 'Enter an email or a mobile number.', 'invalid');
    $password = (string) ($b['password'] ?? '');
    check_password_strength($password);
    $scope = null;
    if ($kind === 'manager') {
        $scope = str_field($b, 'scopeCompanyId', 64);
        if (!preg_match('/^[A-Za-z0-9_.:\-]{1,64}$/', $scope)) throw new ApiError(422, 'scopeCompanyId is invalid.', 'invalid');
    }
    $dup = db()->prepare('SELECT 1 FROM users WHERE (email IS NOT NULL AND email = ?) OR (mobile IS NOT NULL AND mobile = ?) LIMIT 1');
    $dup->execute([$email, $mobile]);
    if ($dup->fetchColumn()) throw new ApiError(409, 'An account with this email or mobile already exists.', 'exists');
    $id = uuid4();
    db()->prepare('INSERT INTO users (id, agency_id, name, email, mobile, password_hash, kind, scope_company_id, is_active, created_at)
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, ?)')
        ->execute([$id, $user['agency_id'], $name, $email, $mobile, password_hash($password, PASSWORD_DEFAULT), $kind, $scope, now()]);
    ok(['id' => $id]);
}

if (preg_match('#^/v1/users/([0-9a-f\-]{36})$#', $path, $m) && $method === 'POST') {
    require_owner($user);
    $b = body();
    $st = db()->prepare('SELECT id, kind FROM users WHERE id = ? AND agency_id = ?');
    $st->execute([$m[1], $user['agency_id']]);
    $target = $st->fetch();
    if (!$target) throw new ApiError(404, 'Person not found.', 'not_found');
    if ($target['kind'] === 'owner') throw new ApiError(403, 'The owner account cannot be changed here.', 'forbidden');
    if (array_key_exists('isActive', $b)) {
        $active = !empty($b['isActive']) ? 1 : 0;
        db()->prepare('UPDATE users SET is_active = ? WHERE id = ?')->execute([$active, $target['id']]);
        if (!$active) db()->prepare('DELETE FROM tokens WHERE user_id = ?')->execute([$target['id']]);
    }
    if (isset($b['password']) && $b['password'] !== '') {
        check_password_strength((string) $b['password']);
        db()->prepare('UPDATE users SET password_hash = ? WHERE id = ?')
            ->execute([password_hash((string) $b['password'], PASSWORD_DEFAULT), $target['id']]);
        db()->prepare('DELETE FROM tokens WHERE user_id = ?')->execute([$target['id']]);
    }
    ok();
}

throw new ApiError(404, 'Unknown endpoint.', 'not_found');
