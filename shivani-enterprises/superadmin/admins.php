<?php
require_once __DIR__ . '/../includes/functions.php';
require_role('super_admin');
$pageTitle = 'Manage Admins';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $name = trim($_POST['name'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $password = (string)($_POST['password'] ?? '');

        if ($name === '' || $username === '' || strlen($password) < 6) {
            flash('error', 'Name, username and a password of at least 6 characters are required.');
        } elseif (!is_valid_username($username)) {
            flash('error', 'Username: sirf letters, numbers, dot (.) ya underscore (_) allowed - koi space nahi (3-30 characters).');
        } elseif ($phone !== '' && !is_valid_mobile($phone)) {
            flash('error', 'Phone: enter a valid 10-digit mobile number (country code jaise +91 chalega).');
        } else {
            $stmt = db()->prepare('SELECT id FROM users WHERE username = ?');
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                flash('error', 'Username already exists.');
            } else {
                $phoneNorm = $phone !== '' ? normalize_mobile($phone) : '';
                $stmt = db()->prepare('INSERT INTO users (role, name, username, email, phone, password_hash) VALUES ("admin",?,?,?,?,?)');
                $stmt->execute([$name, $username, $email ?: null, $phoneNorm ?: null, password_hash($password, PASSWORD_DEFAULT)]);
                log_activity(current_user()['id'], 'admin_created', 'username=' . $username);
                flash('success', 'Admin created successfully.');
            }
        }
    } elseif ($action === 'update') {
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        if ($name === '') {
            flash('error', 'Name is required.');
        } elseif ($phone !== '' && !is_valid_mobile($phone)) {
            flash('error', 'Phone: enter a valid 10-digit mobile number (country code jaise +91 chalega).');
        } else {
            $phoneNorm = $phone !== '' ? normalize_mobile($phone) : '';
            $stmt = db()->prepare('UPDATE users SET name=?, email=?, phone=? WHERE id = ? AND role = "admin"');
            $stmt->execute([$name, $email ?: null, $phoneNorm ?: null, $id]);
            flash('success', 'Admin updated.');
        }
    } elseif ($action === 'toggle_status') {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = db()->prepare('UPDATE users SET status = IF(status="active","disabled","active") WHERE id = ? AND role = "admin"');
        $stmt->execute([$id]);
        flash('success', 'Admin status updated.');
    } elseif ($action === 'reset_password') {
        $id = (int)($_POST['id'] ?? 0);
        $password = (string)($_POST['new_password'] ?? '');
        if (strlen($password) < 6) {
            flash('error', 'New password must be at least 6 characters.');
        } else {
            $stmt = db()->prepare('UPDATE users SET password_hash = ? WHERE id = ? AND role = "admin"');
            $stmt->execute([password_hash($password, PASSWORD_DEFAULT), $id]);
            flash('success', 'Password reset.');
        }
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $custCountStmt = db()->prepare('SELECT COUNT(*) FROM customers WHERE admin_id = ?');
        $custCountStmt->execute([$id]);
        $custCount = (int)$custCountStmt->fetchColumn();
        $invCountStmt = db()->prepare('SELECT COUNT(*) FROM investors WHERE owner_id = ?');
        $invCountStmt->execute([$id]);
        $invCount = (int)$invCountStmt->fetchColumn();

        if ($custCount > 0 || $invCount > 0) {
            flash('error', 'Is admin ke naam customers/investors records hai, isliye delete nahi ho sakta (records surakshit rakhne ke liye). Iske bajaye "Disable" use karein.');
        } else {
            db()->prepare('DELETE FROM users WHERE id = ? AND role = "admin"')->execute([$id]);
            flash('success', 'Admin deleted.');
        }
    }
    redirect('superadmin/admins.php');
}

$admins = db()->query(
    "SELECT u.*,
      (SELECT COUNT(*) FROM customers WHERE admin_id = u.id) AS customer_count,
      (SELECT COUNT(*) FROM investors WHERE owner_id = u.id) AS investor_count
     FROM users u WHERE u.role = 'admin' ORDER BY u.created_at DESC"
)->fetchAll();
require __DIR__ . '/../includes/header.php';
?>
<div class="card">
  <h3 class="mt-0">Add New Admin</h3>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="create">
    <div class="form-row">
      <div class="form-group"><label>Name</label><input name="name" required></div>
      <div class="form-group"><label>Username</label><input name="username" required autocapitalize="off" autocorrect="off" spellcheck="false" data-validate="username"></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label>Email</label><input type="email" name="email"></div>
      <div class="form-group"><label>Phone</label><input name="phone" placeholder="Optional" inputmode="tel" autocomplete="tel" data-validate="mobile"></div>
      <div class="form-group"><label>Password</label><input type="password" name="password" required minlength="6"></div>
    </div>
    <button class="btn" type="submit">Create Admin</button>
  </form>
</div>

<?php foreach ($admins as $a): $fid = 'edit-admin-' . (int)$a['id']; ?>
<form id="<?= $fid ?>" method="post" class="hidden-form">
  <?= csrf_field() ?>
  <input type="hidden" name="action" value="update">
  <input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
</form>
<?php endforeach; ?>
<div class="card">
  <h3 class="mt-0">All Admins</h3>
  <div class="admin-rows">
    <?php foreach ($admins as $a): $fid = 'edit-admin-' . (int)$a['id']; ?>
    <div class="admin-row">
      <div class="admin-row-top">
        <div class="admin-row-name">
          <span class="admin-avatar"><?= e(mb_strtoupper(mb_substr($a['name'], 0, 1))) ?></span>
          <div>
            <div class="admin-row-fullname"><?= e($a['name']) ?></div>
            <div class="admin-row-username">@<?= e($a['username']) ?></div>
          </div>
        </div>
        <span class="badge <?= $a['status'] === 'active' ? 'green' : 'red' ?>"><?= e($a['status']) ?></span>
      </div>

      <div class="admin-row-fields">
        <div class="li-field">
          <label class="li-head">Name</label>
          <input form="<?= $fid ?>" name="name" value="<?= e($a['name']) ?>">
        </div>
        <div class="li-field">
          <label class="li-head">Email</label>
          <input form="<?= $fid ?>" type="email" name="email" value="<?= e($a['email']) ?>" placeholder="Optional">
        </div>
        <div class="li-field">
          <label class="li-head">Phone</label>
          <input form="<?= $fid ?>" name="phone" value="<?= e($a['phone']) ?>" placeholder="Optional" inputmode="tel" autocomplete="tel" data-validate="mobile">
        </div>
      </div>

      <div class="admin-row-actions">
        <button form="<?= $fid ?>" class="btn small" type="submit">Save</button>

        <form method="post" class="admin-reset-form">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="reset_password">
          <input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
          <input type="password" name="new_password" placeholder="New password" minlength="6">
          <button class="btn small secondary" type="submit">Reset Password</button>
        </form>

        <form method="post" onsubmit="return confirm('Change status of this admin?')">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="toggle_status">
          <input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
          <button class="btn small <?= $a['status'] === 'active' ? 'danger' : '' ?>" type="submit"><?= $a['status'] === 'active' ? 'Disable' : 'Enable' ?></button>
        </form>

        <?php if ((int)$a['customer_count'] === 0 && (int)$a['investor_count'] === 0): ?>
        <form method="post" onsubmit="return doubleConfirm('Delete admin <?= e(addslashes($a['name'])) ?> permanently?', 'Are you absolutely sure? This is PERMANENT and cannot be undone.')">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
          <button class="btn small danger" type="submit">Delete</button>
        </form>
        <?php endif; ?>
      </div>
      <?php if ((int)$a['customer_count'] > 0 || (int)$a['investor_count'] > 0): ?>
        <p class="text-muted" style="font-size:12px;margin-top:8px">Is admin ke naam <?= (int)$a['customer_count'] ?> customer(s) / <?= (int)$a['investor_count'] ?> investor(s) hai, isliye delete nahi ho sakta - "Disable" use karein.</p>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>
    <?php if (!$admins): ?><p class="text-muted">No admins yet.</p><?php endif; ?>
  </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
