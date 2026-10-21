<?php
declare(strict_types=1);

final class ApiError extends Exception {
    public function __construct(public int $status, string $message, public string $errCode = 'error') {
        parent::__construct($message);
    }
}

function cfg(string $key, mixed $default = null): mixed {
    static $c = null;
    if ($c === null) {
        $file = __DIR__ . '/../config.php';
        if (!is_file($file)) {
            throw new ApiError(500, 'Server is not configured yet (config.php missing).', 'not_configured');
        }
        $c = require $file;
    }
    return $c[$key] ?? $default;
}

function now(): string {
    return gmdate('Y-m-d H:i:s');
}

function uuid4(): string {
    $b = random_bytes(16);
    $b[6] = chr((ord($b[6]) & 0x0f) | 0x40);
    $b[8] = chr((ord($b[8]) & 0x3f) | 0x80);
    $h = bin2hex($b);
    return sprintf('%s-%s-%s-%s-%s', substr($h, 0, 8), substr($h, 8, 4), substr($h, 12, 4), substr($h, 16, 4), substr($h, 20));
}

function json_out(int $status, array $body): never {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    header('X-Content-Type-Options: nosniff');
    echo json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function body(): array {
    static $b = null;
    if ($b !== null) return $b;
    $raw = file_get_contents('php://input');
    if ($raw === false || $raw === '') return $b = [];
    if (strlen($raw) > 4 * 1024 * 1024) throw new ApiError(413, 'Request is too large.', 'too_large');
    $j = json_decode($raw, true);
    if (!is_array($j)) throw new ApiError(400, 'Request body must be JSON.', 'bad_json');
    return $b = $j;
}

function str_field(array $a, string $k, int $max, bool $required = true): string {
    $v = $a[$k] ?? '';
    if (!is_string($v)) $v = '';
    $v = trim($v);
    if ($required && $v === '') throw new ApiError(422, "$k is required.", 'invalid');
    if (mb_strlen($v) > $max) throw new ApiError(422, "$k is too long.", 'invalid');
    return $v;
}

function client_ip(): string {
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

function normalize_mobile(string $m): ?string {
    $d = preg_replace('/\D+/', '', $m) ?? '';
    if ($d === '') return null;
    if (strlen($d) < 7 || strlen($d) > 15) throw new ApiError(422, 'Enter a valid mobile number.', 'invalid');
    return $d;
}

function normalize_email(string $e): ?string {
    $e = strtolower(trim($e));
    if ($e === '') return null;
    if (!filter_var($e, FILTER_VALIDATE_EMAIL) || strlen($e) > 190) {
        throw new ApiError(422, 'Enter a valid email address.', 'invalid');
    }
    return $e;
}

function valid_uuid(?string $v): bool {
    return is_string($v) && preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/', $v) === 1;
}
