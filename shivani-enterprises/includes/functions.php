<?php
require_once __DIR__ . '/auth.php';

function base_url(string $path = ''): string
{
    return rtrim(APP_URL, '/') . '/' . ltrim($path, '/');
}

function e($value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function money($amount): string
{
    return '₹' . number_format((float)$amount, 2);
}

function csrf_token(): string
{
    start_secure_session();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Prevents any layer (browser back/forward cache, LiteSpeed/Varnish/CDN
 * page cache) from serving a stale copy of a page that has a CSRF token
 * baked into it - a stale token would never match the live session and
 * every form submit would fail with "CSRF check failed".
 */
function no_store_headers(): void
{
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf(): void
{
    start_secure_session();
    $token = $_POST['csrf_token'] ?? '';
    if (!$token || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(400);
        die('Invalid or expired form submission (CSRF check failed). Please go back and try again.');
    }
}

function flash(string $type, string $message): void
{
    start_secure_session();
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function get_flashes(): array
{
    start_secure_session();
    $f = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $f;
}

function redirect(string $path): void
{
    header('Location: ' . base_url($path));
    exit;
}

/** Generates the next invoice number like SE-000123 */
function next_invoice_no(): string
{
    $prefix = get_setting('invoice_prefix', 'SE');
    $stmt = db()->query('SELECT MAX(id) AS max_id FROM sales');
    $next = ((int)($stmt->fetch()['max_id'] ?? 0)) + 1;
    return $prefix . '-' . str_pad((string)$next, 6, '0', STR_PAD_LEFT);
}

function get_setting(string $key, string $default = ''): string
{
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        foreach (db()->query('SELECT setting_key, setting_value FROM settings') as $row) {
            $cache[$row['setting_key']] = $row['setting_value'];
        }
    }
    return $cache[$key] ?? $default;
}

/**
 * Validates + moves an uploaded image (customer photo). Returns the stored
 * relative path (e.g. "uploads/customers/xxxx.jpg") or null if no file sent.
 */
function handle_photo_upload(string $field, string $subdir = 'customers'): ?string
{
    if (empty($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    $file = $_FILES[$field];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Photo upload failed (error code ' . $file['error'] . ').');
    }
    if ($file['size'] > MAX_UPLOAD_MB * 1024 * 1024) {
        throw new RuntimeException('Photo is too large. Max ' . MAX_UPLOAD_MB . 'MB allowed.');
    }
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    if (!isset($allowed[$mime])) {
        throw new RuntimeException('Only JPG, PNG or WEBP images are allowed.');
    }
    $ext = $allowed[$mime];
    $name = bin2hex(random_bytes(16)) . '.' . $ext;
    $destDir = __DIR__ . '/../uploads/' . $subdir . '/';
    if (!is_dir($destDir)) {
        mkdir($destDir, 0755, true);
    }
    if (!move_uploaded_file($file['tmp_name'], $destDir . $name)) {
        throw new RuntimeException('Could not save uploaded photo.');
    }
    return 'uploads/' . $subdir . '/' . $name;
}

/**
 * Validates + moves an uploaded document (pdf/image) for a customer.
 */
function handle_document_upload(string $field): ?array
{
    if (empty($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    $file = $_FILES[$field];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Document upload failed (error code ' . $file['error'] . ').');
    }
    if ($file['size'] > MAX_UPLOAD_MB * 1024 * 1024) {
        throw new RuntimeException('File is too large. Max ' . MAX_UPLOAD_MB . 'MB allowed.');
    }
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'application/pdf' => 'pdf'];
    if (!isset($allowed[$mime])) {
        throw new RuntimeException('Only JPG, PNG or PDF documents are allowed.');
    }
    $ext = $allowed[$mime];
    $name = bin2hex(random_bytes(16)) . '.' . $ext;
    $destDir = __DIR__ . '/../uploads/documents/';
    if (!is_dir($destDir)) {
        mkdir($destDir, 0755, true);
    }
    if (!move_uploaded_file($file['tmp_name'], $destDir . $name)) {
        throw new RuntimeException('Could not save uploaded document.');
    }
    return ['path' => 'uploads/documents/' . $name, 'original_name' => basename($file['name'])];
}

/** Builds a wa.me click-to-chat link with a prefilled message. */
function whatsapp_link(string $mobile, string $message): string
{
    $digits = preg_replace('/\D+/', '', $mobile);
    if (strlen($digits) === 10) {
        $digits = '91' . $digits; // default India country code
    }
    return 'https://wa.me/' . $digits . '?text=' . rawurlencode($message);
}

/** Central list of theme keys -> [gradient-start, gradient-end, display name]. */
function theme_palette(): array
{
    return [
        'teal' => ['#0f766e', '#06b6d4', 'Teal'],
        'blue' => ['#2563eb', '#4f46e5', 'Blue'],
        'lightblue' => ['#0ea5e9', '#22d3ee', 'Light Blue'],
        'green' => ['#059669', '#84cc16', 'Green'],
        'purple' => ['#7c3aed', '#ec4899', 'Purple'],
        'orange' => ['#ea580c', '#f43f5e', 'Orange'],
        'black' => ['#6366f1', '#a855f7', 'Black'],
        'darkpro' => ['#5eead4', '#22d3ee', 'Dark Pro'],
    ];
}

function normalize_mobile(string $mobile): string
{
    $digits = preg_replace('/\D+/', '', $mobile);
    // Keep last 10 digits as the canonical form for duplicate-checking/storage.
    return strlen($digits) > 10 ? substr($digits, -10) : $digits;
}

/**
 * True for a real 10-digit Indian mobile number (starts 6-9), however it
 * was typed - with spaces, dashes, +91, a leading 0, or pasted straight
 * from a contact card. normalize_mobile() already strips all of that down
 * to the last 10 digits, so this just checks the result looks like a
 * mobile number and not, say, a landline or a typo.
 */
function is_valid_mobile(string $raw): bool
{
    return (bool)preg_match('/^[6-9]\d{9}$/', normalize_mobile($raw));
}

/** Username: letters/numbers/dot/underscore only, no spaces, 3-30 chars. */
function is_valid_username(string $username): bool
{
    return (bool)preg_match('/^[a-zA-Z0-9_.]{3,30}$/', $username);
}

/**
 * Stateless, tamper-proof "verify this document is genuine" signature.
 * No database column needed - the signature is an HMAC over the exact
 * values printed on the invoice/statement, keyed with APP_SECRET. verify.php
 * recomputes it from the URL's own query params and rejects a mismatch,
 * so an altered link (different amount, different customer, etc.) is
 * immediately detected as invalid.
 */
function verify_sign(array $parts): string
{
    // 16 hex chars (64 bits) - plenty to stop casual link tampering, and
    // short enough to keep the verify URL well inside the QR code's byte
    // budget (kept to versions 1-6, ~100 bytes max, so the URL must stay
    // short - the signed values are looked up live from the DB by
    // verify.php rather than carried in the URL, keeping it tiny).
    return substr(hash_hmac('sha256', implode('|', $parts), APP_SECRET), 0, 16);
}

function invoice_verify_url(array $sale): string
{
    $sig = verify_sign(['invoice', $sale['id']]);
    return base_url('verify.php') . '?i=' . $sale['id'] . '&s=' . $sig;
}

function statement_verify_url(array $customer): string
{
    $sig = verify_sign(['statement', $customer['id']]);
    return base_url('verify.php') . '?c=' . $customer['id'] . '&s=' . $sig;
}

function investor_statement_verify_url(array $investor): string
{
    $sig = verify_sign(['investor', $investor['id']]);
    return base_url('verify.php') . '?inv=' . $investor['id'] . '&s=' . $sig;
}
