<?php
/**
 * DIAGNOSTIC ONLY - upload this, open it in browser, RELOAD the page 2-3 times.
 * Delete this file once the CSRF issue is fixed (it's not needed by the app).
 */
require_once __DIR__ . '/includes/auth.php';
startSecureSession();

if (!isset($_SESSION['hit_count'])) {
    $_SESSION['hit_count'] = 0;
}
$_SESSION['hit_count']++;

header('Content-Type: text/plain');
echo "Session ID: " . session_id() . "\n";
echo "Hit count (should go UP each reload): " . $_SESSION['hit_count'] . "\n";
echo "Session save path: " . session_save_path() . "\n";
echo "Save path writable: " . (is_writable(session_save_path() ?: sys_get_temp_dir()) ? 'YES' : 'NO') . "\n";
echo "HTTPS detected: " . (isHttpsRequest() ? 'YES' : 'NO') . "\n";
echo "Cookie sent by browser (PHPSESSID): " . ($_COOKIE[session_name()] ?? 'NOT SENT') . "\n";
echo "\nIf 'Hit count' stays at 1 on every reload -> sessions are NOT persisting on this hosting.\n";
echo "If 'Hit count' goes up (1, 2, 3...) -> sessions work fine, problem was page caching (already fixed).\n";
