<?php
/**
 * Expects $pageTitle to be set before including this file.
 * Requires functions.php already loaded and user already authenticated
 * by the calling page.
 */
no_store_headers();
$u = current_user();
$pageTitle = $pageTitle ?? APP_NAME;
$currentScript = basename($_SERVER['SCRIPT_NAME']);
function nav_active(string $file): string
{
    global $currentScript;
    return $currentScript === $file ? ' class="active"' : '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
<title><?= e($pageTitle) ?> · <?= e(APP_NAME) ?></title>
<link rel="stylesheet" href="<?= e(base_url('assets/css/style.css')) ?>">
<link rel="manifest" href="<?= e(base_url('manifest.json')) ?>">
<link rel="apple-touch-icon" href="<?= e(base_url('assets/icons/apple-touch-icon.png')) ?>">
<link rel="icon" href="<?= e(base_url('assets/icons/icon-192.png')) ?>">
<meta name="theme-color" content="#0f766e">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="<?= e(APP_NAME) ?>">
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
<body data-sw-url="<?= e(base_url('sw.js')) ?>">
<div class="sidebar-overlay"></div>
<div class="app-shell">
  <aside class="sidebar">
    <div class="brand">
      <span class="brand-text"><?= e(APP_NAME) ?><small>Sales Ledger</small></span>
      <button type="button" class="sidebar-close" aria-label="Close menu">&times;</button>
    </div>
    <nav>
      <?php if ($u && $u['role'] === 'super_admin'): $base = 'superadmin/'; ?>
        <a<?= nav_active('dashboard.php') ?> href="<?= e(base_url($base.'dashboard.php')) ?>">Dashboard</a>
        <a<?= nav_active('admins.php') ?> href="<?= e(base_url($base.'admins.php')) ?>">Admins</a>
        <a<?= nav_active('products.php') ?> href="<?= e(base_url($base.'products.php')) ?>">Products</a>
        <a<?= nav_active('customers.php') ?> href="<?= e(base_url($base.'customers.php')) ?>">All Customers</a>
        <a<?= nav_active('sales.php') ?> href="<?= e(base_url($base.'sales.php')) ?>">All Sales</a>
        <a<?= nav_active('payments.php') ?> href="<?= e(base_url($base.'payments.php')) ?>">All Payments</a>
        <a<?= nav_active('ledger.php') ?> href="<?= e(base_url($base.'ledger.php')) ?>">Global Ledger</a>
        <a<?= nav_active('reports_admin_wise.php') ?> href="<?= e(base_url($base.'reports_admin_wise.php')) ?>">Admin-wise Report</a>
        <a<?= nav_active('followups.php') ?> href="<?= e(base_url($base.'followups.php')) ?>">Follow-ups / Commitments</a>
        <a<?= nav_active('backup.php') ?> href="<?= e(base_url($base.'backup.php')) ?>">Backup</a>
        <a<?= nav_active('settings.php') ?> href="<?= e(base_url($base.'settings.php')) ?>">Settings</a>
        <a<?= nav_active('change_password.php') ?> href="<?= e(base_url('change_password.php')) ?>">Change Password</a>
      <?php elseif ($u): $base = 'admin/'; ?>
        <a<?= nav_active('dashboard.php') ?> href="<?= e(base_url($base.'dashboard.php')) ?>">Dashboard</a>
        <a<?= nav_active('customers.php') ?> href="<?= e(base_url($base.'customers.php')) ?>">My Customers</a>
        <a<?= nav_active('sales.php') ?> href="<?= e(base_url($base.'sales.php')) ?>">Sales / Invoices</a>
        <a<?= nav_active('payments.php') ?> href="<?= e(base_url($base.'payments.php')) ?>">Payments</a>
        <a<?= nav_active('followups.php') ?> href="<?= e(base_url($base.'followups.php')) ?>">Follow-ups</a>
        <a<?= nav_active('reports.php') ?> href="<?= e(base_url($base.'reports.php')) ?>">My Reports</a>
        <a<?= nav_active('change_password.php') ?> href="<?= e(base_url('change_password.php')) ?>">Change Password</a>
      <?php endif; ?>
    </nav>
  </aside>
  <div class="main">
    <div class="topbar">
      <div class="topbar-left">
        <button type="button" class="menu-toggle" aria-label="Open menu">&#9776;</button>
        <strong><?= e($pageTitle) ?></strong>
      </div>
      <?php if ($u): $searchTarget = $u['role'] === 'super_admin' ? 'superadmin/customers.php' : 'admin/customers.php'; ?>
      <form class="topbar-search" method="get" action="<?= e(base_url($searchTarget)) ?>">
        <input type="search" name="q" placeholder="Search customer / mobile…">
      </form>
      <?php endif; ?>
      <div class="topbar-right">
        <details class="theme-switcher">
          <summary title="Change theme"><span class="theme-dot"></span></summary>
          <div class="theme-panel">
            <?php foreach (theme_palette() as $themeKey => $g): ?>
              <button type="button" class="theme-swatch" data-theme="<?= e($themeKey) ?>" style="background:linear-gradient(135deg,<?= e($g[0]) ?>,<?= e($g[1]) ?>)" title="<?= e($g[2]) ?>"></button>
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
