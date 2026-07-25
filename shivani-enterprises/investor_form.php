<?php
require_once __DIR__ . '/includes/functions.php';
$u = require_login();
$pageTitle = 'Investor';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$investor = null;
if ($id) {
    $stmt = db()->prepare('SELECT * FROM investors WHERE id = ?');
    $stmt->execute([$id]);
    $investor = $stmt->fetch();
    if (!$investor) { http_response_code(404); die('Investor not found.'); }
    if ((int)$investor['owner_id'] !== (int)$u['id']) {
        http_response_code(403); die('Access denied.');
    }
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim($_POST['name'] ?? '');
    $mobile = trim($_POST['mobile'] ?? '');
    $notes = trim($_POST['notes'] ?? '');

    if ($name === '') $errors[] = 'Name is required.';

    if (!$errors) {
        if ($id) {
            $stmt = db()->prepare('UPDATE investors SET name=?, mobile=?, notes=? WHERE id=?');
            $stmt->execute([$name, $mobile ?: null, $notes ?: null, $id]);
            flash('success', 'Investor updated.');
        } else {
            $stmt = db()->prepare('INSERT INTO investors (owner_id, name, mobile, notes) VALUES (?,?,?,?)');
            $stmt->execute([$u['id'], $name, $mobile ?: null, $notes ?: null]);
            $id = (int)db()->lastInsertId();
            flash('success', 'Investor added.');
        }
        redirect('investor_view.php?id=' . $id);
    }
}

require __DIR__ . '/includes/header.php';
?>
<div class="card">
  <h3 class="mt-0"><?= $investor ? 'Edit Investor' : 'Add New Investor' ?></h3>
  <?php foreach ($errors as $err): ?><div class="alert error"><?= e($err) ?></div><?php endforeach; ?>
  <form method="post">
    <?= csrf_field() ?>
    <div class="form-row">
      <div class="form-group"><label>Name *</label><input name="name" required value="<?= e($investor['name'] ?? '') ?>"></div>
      <div class="form-group"><label>Mobile</label><input name="mobile" value="<?= e($investor['mobile'] ?? '') ?>"></div>
    </div>
    <div class="form-group"><label>Notes</label><textarea name="notes" rows="2" placeholder="Optional"><?= e($investor['notes'] ?? '') ?></textarea></div>
    <button class="btn" type="submit"><?= $investor ? 'Save Changes' : 'Add Investor' ?></button>
    <a class="btn secondary" href="<?= e(base_url('investors.php')) ?>">Cancel</a>
  </form>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
