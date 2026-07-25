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
        } else {
            $stmt = db()->prepare('SELECT id FROM users WHERE username = ?');
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                flash('error', 'Username already exists.');
            } else {
                $stmt = db()->prepare('INSERT INTO users (role, name, username, email, phone, password_hash) VALUES ("admin",?,?,?,?,?)');
                $stmt->execute([$name, $username, $email ?: null, $phone ?: null, password_hash($password, PASSWORD_DEFAULT)]);
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
        } else {
            $stmt = db()->prepare('UPDATE users SET name=?, email=?, phone=? WHERE id = ? AND role = "admin"');
            $stmt->execute([$name, $email ?: null, $phone ?: null, $id]);
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
    }
    redirect('superadmin/admins.php');
}

$admins = db()->query('SELECT * FROM users WHERE role = "admin" ORDER BY created_at DESC')->fetchAll();
require __DIR__ . '/../includes/header.php';
?>
<div class="card">
  <h3 class="mt-0">Add New Admin</h3>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="create">
    <div class="form-row">
      <div class="form-group"><label>Name</label><input name="name" required></div>
      <div class="form-group"><label>Username</label><input name="username" required></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label>Email</label><input type="email" name="email"></div>
      <div class="form-group"><label>Phone</label><input name="phone"></div>
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
  <table>
    <tr><th>Name</th><th>Username</th><th>Email</th><th>Phone</th><th>Status</th><th></th><th>Reset Password</th><th></th></tr>
    <?php foreach ($admins as $a): $fid = 'edit-admin-' . (int)$a['id']; ?>
    <tr>
      <td><input form="<?= $fid ?>" name="name" value="<?= e($a['name']) ?>" style="min-width:120px"></td>
      <td><?= e($a['username']) ?></td>
      <td><input form="<?= $fid ?>" type="email" name="email" value="<?= e($a['email']) ?>" style="min-width:140px"></td>
      <td><input form="<?= $fid ?>" name="phone" value="<?= e($a['phone']) ?>" style="min-width:110px"></td>
      <td><span class="badge <?= $a['status'] === 'active' ? 'green' : 'red' ?>"><?= e($a['status']) ?></span></td>
      <td><button form="<?= $fid ?>" class="btn small" type="submit">Save</button></td>
      <td>
        <form method="post" style="display:flex;gap:6px">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="reset_password">
          <input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
          <input type="password" name="new_password" placeholder="New password" minlength="6" style="width:130px">
          <button class="btn small" type="submit">Reset</button>
        </form>
      </td>
      <td>
        <form method="post" onsubmit="return confirm('Change status of this admin?')">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="toggle_status">
          <input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
          <button class="btn small <?= $a['status'] === 'active' ? 'danger' : '' ?>" type="submit"><?= $a['status'] === 'active' ? 'Disable' : 'Enable' ?></button>
        </form>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
