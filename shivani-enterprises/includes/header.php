<?php
/**
 * Expects $pageTitle to be set before including this file.
 * Requires functions.php already loaded and user already authenticated
 * by the calling page.
 */
$u = current_user();
$pageTitle = $pageTitle ?? APP_NAME;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle) ?> · <?= e(APP_NAME) ?></title>
<link rel="stylesheet" href="<?= e(base_url('assets/css/style.css')) ?>">
</head>
<body>
<div class="app-shell">
  <aside class="sidebar">
    <div class="brand"><?= e(APP_NAME) ?><small>Sales Ledger</small></div>
    <nav>
      <?php if ($u && $u['role'] === 'super_admin'): $base = 'superadmin/'; ?>
        <a href="<?= e(base_url($base.'dashboard.php')) ?>">Dashboard</a>
        <a href="<?= e(base_url($base.'admins.php')) ?>">Admins</a>
        <a href="<?= e(base_url($base.'products.php')) ?>">Products</a>
        <a href="<?= e(base_url($base.'customers.php')) ?>">All Customers</a>
        <a href="<?= e(base_url($base.'sales.php')) ?>">All Sales</a>
        <a href="<?= e(base_url($base.'payments.php')) ?>">All Payments</a>
        <a href="<?= e(base_url($base.'ledger.php')) ?>">Global Ledger</a>
        <a href="<?= e(base_url($base.'reports_admin_wise.php')) ?>">Admin-wise Report</a>
        <a href="<?= e(base_url($base.'followups.php')) ?>">Follow-ups / Commitments</a>
        <a href="<?= e(base_url($base.'backup.php')) ?>">Backup</a>
        <a href="<?= e(base_url($base.'settings.php')) ?>">Settings</a>
        <a href="<?= e(base_url('change_password.php')) ?>">Change Password</a>
      <?php elseif ($u): $base = 'admin/'; ?>
        <a href="<?= e(base_url($base.'dashboard.php')) ?>">Dashboard</a>
        <a href="<?= e(base_url($base.'customers.php')) ?>">My Customers</a>
        <a href="<?= e(base_url($base.'sales.php')) ?>">Sales / Invoices</a>
        <a href="<?= e(base_url($base.'payments.php')) ?>">Payments</a>
        <a href="<?= e(base_url($base.'followups.php')) ?>">Follow-ups</a>
        <a href="<?= e(base_url($base.'reports.php')) ?>">My Reports</a>
        <a href="<?= e(base_url('change_password.php')) ?>">Change Password</a>
      <?php endif; ?>
    </nav>
  </aside>
  <div class="main">
    <div class="topbar">
      <strong><?= e($pageTitle) ?></strong>
      <div>
        <?php if ($u): ?>
          <span class="text-muted"><?= e($u['name']) ?> (<?= e($u['role']) ?>)</span>
          &nbsp;|&nbsp; <a href="<?= e(base_url('logout.php')) ?>">Logout</a>
        <?php endif; ?>
      </div>
    </div>
    <div class="content">
      <?php foreach (get_flashes() as $f): ?>
        <div class="alert <?= e($f['type']) ?>"><?= e($f['message']) ?></div>
      <?php endforeach; ?>
