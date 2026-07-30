<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

startSecureSession();
if (currentUser()) {
    redirect('dashboard.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($username !== '' && $password !== '' && attemptLogin($username, $password)) {
        redirect('dashboard.php');
    }
    $error = 'Invalid username or password.';
}

$pageTitle = 'Login';
require __DIR__ . '/includes/header.php';
?>
<div class="login-card">
  <h1>Lead CRM</h1>
  <p class="page-subtitle" style="text-align:center;">Login to continue</p>
  <?php if ($error): ?><div class="alert alert-error"><?= h($error) ?></div><?php endif; ?>
  <form method="post" action="login.php">
    <?= csrfField() ?>
    <label for="username">Username</label>
    <input type="text" id="username" name="username" required autofocus>
    <label for="password">Password</label>
    <input type="password" id="password" name="password" required>
    <button type="submit" class="btn" style="width:100%;margin-top:20px;">Login</button>
  </form>
  <p class="small muted" style="text-align:center;margin-top:16px;">Default: admin / ChangeMe123! (change it in Settings after login)</p>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
