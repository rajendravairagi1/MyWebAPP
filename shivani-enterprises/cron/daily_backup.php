<?php
/**
 * Daily backup runner.
 *
 * cPanel "Run a command" cron job:
 *   php /home/USER/shivani-enterprises/cron/daily_backup.php
 *
 * cPanel "URL" style cron job (if shell cron isn't available), hit:
 *   https://your-domain.com/shivani-enterprises/cron/daily_backup.php?token=YOUR_APP_SECRET
 * (uses APP_SECRET from config.php as a simple shared-secret so randoms
 * on the internet can't trigger backups.)
 */
require_once __DIR__ . '/../includes/backup.php';

$isCli = PHP_SAPI === 'cli';
if (!$isCli) {
    $token = $_GET['token'] ?? '';
    if (!hash_equals(APP_SECRET, $token)) {
        http_response_code(403);
        die('Forbidden.');
    }
}

try {
    $file = run_backup();
    $msg = "Backup created: $file\n";
} catch (Throwable $e) {
    $msg = 'Backup FAILED: ' . $e->getMessage() . "\n";
}

if ($isCli) {
    echo $msg;
} else {
    header('Content-Type: text/plain');
    echo $msg;
}
