<?php
require_once __DIR__ . '/includes/functions.php';
$u = require_login();
$pageTitle = 'Change Password';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $current = (string)($_POST['current_password'] ?? '');
    $new = (string)($_POST['new_password'] ?? '');
    $confirm = (string)($_POST['confirm_password'] ?? '');

    $stmt = db()->prepare('SELECT password_hash FROM users WHERE id = ?');
    $stmt->execute([$u['id']]);
    $hash = $stmt->fetchColumn();

    if (!password_verify($current, $hash)) {
        $errors[] = 'Current password is incorrect.';
    }
    if (strlen($new) < 6) {
        $errors[] = 'New password must be at least 6 characters.';
    }
    if ($new !== $confirm) {
        $errors[] = 'New password and confirmation do not match.';
    }

    if (!$errors) {
        $stmt = db()->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
        $stmt->execute([password_hash($new, PASSWORD_DEFAULT), $u['id']]);
        log_activity($u['id'], 'password_changed', null);
        flash('success', 'Password changed successfully.');
        redirect($u['role'] === 'super_admin' ? 'superadmin/dashboard.php' : 'admin/dashboard.php');
    }
}

require __DIR__ . '/includes/header.php';
?>
<div class="card" style="max-width:420px">
  <h3 class="mt-0">Change Password</h3>
  <?php foreach ($errors as $err): ?><div class="alert error"><?= e($err) ?></div><?php endforeach; ?>
  <form method="post">
    <?= csrf_field() ?>
    <div class="form-group"><label>Current Password</label><input type="password" name="current_password" required></div>
    <div class="form-group"><label>New Password</label><input type="password" name="new_password" required minlength="6"></div>
    <div class="form-group"><label>Confirm New Password</label><input type="password" name="confirm_password" required minlength="6"></div>
    <button class="btn" type="submit">Update Password</button>
  </form>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
