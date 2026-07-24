<?php
require_once __DIR__ . '/includes/functions.php';
$u = require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('login.php'); }
verify_csrf();

$id = (int)($_POST['id'] ?? 0);
$status = $_POST['status'] ?? '';
$returnTo = $_POST['return_to'] ?? ($u['role'] === 'super_admin' ? 'superadmin/followups.php' : 'admin/followups.php');

if (!in_array($status, ['pending', 'done', 'cancelled'], true)) {
    flash('error', 'Invalid status.');
    redirect($returnTo);
}

$stmt = db()->prepare('SELECT * FROM followups WHERE id = ?');
$stmt->execute([$id]);
$fu = $stmt->fetch();
if (!$fu) { http_response_code(404); die('Follow-up not found.'); }
if ($u['role'] !== 'super_admin' && (int)$fu['admin_id'] !== (int)$u['id']) {
    http_response_code(403); die('Access denied.');
}

$stmt = db()->prepare('UPDATE followups SET status = ? WHERE id = ?');
$stmt->execute([$status, $id]);
flash('success', 'Follow-up marked as ' . $status . '.');
redirect($returnTo);
