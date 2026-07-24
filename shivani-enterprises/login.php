<?php
require_once __DIR__ . '/includes/functions.php';
no_store_headers();

if (current_user()) {
    redirect(is_super_admin() ? 'superadmin/dashboard.php' : 'admin/dashboard.php');
}

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $username = trim($_POST['username'] ?? '');
    $password = (string)($_POST['password'] ?? '');
    if ($username === '' || $password === '') {
        $error = 'Username and password are required.';
    } elseif (attempt_login($username, $password)) {
        redirect(is_super_admin() ? 'superadmin/dashboard.php' : 'admin/dashboard.php');
    } else {
        $error = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
<title>Login · <?= e(APP_NAME) ?></title>
<link rel="stylesheet" href="<?= e(base_url('assets/css/style.css')) ?>">
<script>
  (function () {
    try {
      var t = localStorage.getItem('shivani_theme');
      if (t) document.documentElement.setAttribute('data-theme', t);
    } catch (e) {}
  })();
</script>
</head>
<body>
<div class="login-wrap">
  <div class="login-box">
    <h1><?= e(APP_NAME) ?></h1>
    <p class="sub">Sales Ledger &amp; CRM Login</p>
    <div class="login-theme-row">
      <?php foreach ([
        'teal' => '#0f766e', 'blue' => '#2563eb', 'lightblue' => '#0284c7',
        'green' => '#16a34a', 'purple' => '#7c3aed', 'orange' => '#ea580c', 'black' => '#111827',
      ] as $themeKey => $themeColor): ?>
        <button type="button" class="theme-swatch" data-theme="<?= e($themeKey) ?>" style="background:<?= e($themeColor) ?>;width:28px;height:28px" title="<?= e(ucfirst($themeKey)) ?>"></button>
      <?php endforeach; ?>
    </div>
    <?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
    <form method="post" autocomplete="off">
      <?= csrf_field() ?>
      <div class="form-group">
        <label>Username</label>
        <input type="text" name="username" required autofocus autocapitalize="off">
      </div>
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" required>
      </div>
      <button class="btn" type="submit" style="width:100%">Login</button>
    </form>
  </div>
</div>
<script src="<?= e(base_url('assets/js/app.js')) ?>"></script>
</body>
</html>
