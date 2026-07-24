<?php
/**
 * Expects $pageTitle to be set before including this file.
 * Requires functions.php already loaded and user already authenticated
 * by the calling page.
 */
no_store_headers();
$u = current_user();
$pageTitle = $pageTitle ?? APP_NAME;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
<title><?= e($pageTitle) ?> · <?= e(APP_NAME) ?></title>
<link rel="stylesheet" href="<?= e(base_url('assets/css/style.css')) ?>">
<script>
  // Applied before CSS paints so the page never flashes the wrong theme.
  (function () {
    try {
      var t = localStorage.getItem('shivani_theme');
      if (t) document.documentElement.setAttribute('data-theme', t);
    } catch (e) {}
  })();
</script>
</head>
<body>
<div class="sidebar-overlay"></div>
<div class="app-shell">
  <aside class="sidebar">
    <div class="brand">
      <span class="brand-text"><?= e(APP_NAME) ?><small>Sales Ledger</small></span>
      <button type="button" class="sidebar-close" aria-label="Close menu">&times;</button>
    </div>
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
      <div class="topbar-left">
        <button type="button" class="menu-toggle" aria-label="Open menu">&#9776;</button>
        <strong><?= e($pageTitle) ?></strong>
      </div>
      <div class="topbar-right">
        <details class="theme-switcher">
          <summary title="Change theme"><span class="theme-dot"></span></summary>
          <div class="theme-panel">
            <?php foreach ([
              'teal' => '#0f766e', 'blue' => '#2563eb', 'lightblue' => '#0284c7',
              'green' => '#16a34a', 'purple' => '#7c3aed', 'orange' => '#ea580c', 'black' => '#111827',
            ] as $themeKey => $themeColor): ?>
              <button type="button" class="theme-swatch" data-theme="<?= e($themeKey) ?>" style="background:<?= e($themeColor) ?>" title="<?= e(ucfirst($themeKey)) ?>"></button>
            <?php endforeach; ?>
          </div>
        </details>
        <?php if ($u): ?>
          <span class="text-muted"><?= e($u['name']) ?> (<?= e($u['role']) ?>)</span>
          <a href="<?= e(base_url('logout.php')) ?>">Logout</a>
        <?php endif; ?>
      </div>
    </div>
    <div class="content">
      <?php foreach (get_flashes() as $f): ?>
        <div class="alert <?= e($f['type']) ?>"><?= e($f['message']) ?></div>
      <?php endforeach; ?>
