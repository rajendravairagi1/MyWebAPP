<?php
require_once __DIR__ . '/includes/functions.php';
$u = require_login();
$pageTitle = 'Customer';

$id = resolve_id('customer');
$customer = null;
if ($id) {
    $stmt = db()->prepare('SELECT * FROM customers WHERE id = ?');
    $stmt->execute([$id]);
    $customer = $stmt->fetch();
    if (!$customer) { http_response_code(404); die('Customer not found.'); }
    if ($u['role'] !== 'super_admin' && (int)$customer['admin_id'] !== (int)$u['id']) {
        http_response_code(403); die('Access denied.');
    }
}

$admins = [];
if ($u['role'] === 'super_admin') {
    $admins = db()->query('SELECT id, name FROM users WHERE role = "admin" AND status = "active" ORDER BY name')->fetchAll();
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim($_POST['name'] ?? '');
    $shop = trim($_POST['shop_name'] ?? '');
    $place = trim($_POST['place'] ?? '');
    $mobileRaw = trim($_POST['mobile'] ?? '');
    $altMobile = trim($_POST['alt_mobile'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $adminId = $u['role'] === 'super_admin' ? (int)($_POST['admin_id'] ?? 0) : (int)$u['id'];

    if ($name === '') $errors[] = 'Name is required.';
    if ($mobileRaw === '' || !is_valid_mobile($mobileRaw)) $errors[] = 'Mobile Number: enter a valid 10-digit Indian mobile number (e.g. 9876543210). Country code (+91) is fine too.';
    if ($altMobile !== '' && !is_valid_mobile($altMobile)) $errors[] = 'Alternate Mobile: not a valid 10-digit mobile number.';
    if ($adminId <= 0) $errors[] = 'Please select the admin this customer belongs to.';

    $mobile = normalize_mobile($mobileRaw);
    $altMobile = $altMobile !== '' ? normalize_mobile($altMobile) : '';

    if (!$errors) {
        $dupStmt = db()->prepare('SELECT id, name FROM customers WHERE mobile = ? AND id != ?');
        $dupStmt->execute([$mobile, $id]);
        $dup = $dupStmt->fetch();
        if ($dup) {
            $errors[] = 'A customer with this mobile number already exists: ' . $dup['name'] . ' (duplicate not allowed).';
        }
    }

    $photoPath = null;
    if (!$errors) {
        try {
            $photoPath = handle_photo_upload('photo');
        } catch (RuntimeException $e) {
            $errors[] = $e->getMessage();
        }
    }

    if (!$errors) {
        if ($id) {
            $sql = 'UPDATE customers SET name=?, shop_name=?, place=?, mobile=?, alt_mobile=?, address=?, admin_id=?' . ($photoPath ? ', photo=?' : '') . ' WHERE id=?';
            $params = [$name, $shop ?: null, $place ?: null, $mobile, $altMobile ?: null, $address ?: null, $adminId];
            if ($photoPath) $params[] = $photoPath;
            $params[] = $id;
            db()->prepare($sql)->execute($params);
            flash('success', 'Customer updated.');
        } else {
            $stmt = db()->prepare('INSERT INTO customers (name, shop_name, place, mobile, alt_mobile, address, photo, admin_id, created_by) VALUES (?,?,?,?,?,?,?,?,?)');
            $stmt->execute([$name, $shop ?: null, $place ?: null, $mobile, $altMobile ?: null, $address ?: null, $photoPath, $adminId, $u['id']]);
            $id = (int)db()->lastInsertId();
            flash('success', 'Customer added.');
        }
        redirect('customer_view.php?c=' . sign_id('customer', $id));
    }
}

require __DIR__ . '/includes/header.php';
?>
<div class="card">
  <h3 class="mt-0"><?= $customer ? 'Edit Customer' : 'Add New Customer' ?></h3>
  <?php foreach ($errors as $err): ?><div class="alert error"><?= e($err) ?></div><?php endforeach; ?>
  <form method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="form-row">
      <div class="form-group"><label>Name *</label><input name="name" required value="<?= e($customer['name'] ?? '') ?>"></div>
      <div class="form-group"><label>Shop / Business Name</label><input name="shop_name" value="<?= e($customer['shop_name'] ?? '') ?>"></div>
      <div class="form-group"><label>Place / Area</label><input name="place" value="<?= e($customer['place'] ?? '') ?>"></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label>Mobile Number *</label><input name="mobile" required value="<?= e($customer['mobile'] ?? '') ?>" placeholder="10 digit mobile" inputmode="tel" autocomplete="tel" data-validate="mobile"></div>
      <div class="form-group"><label>Alternate Mobile</label><input name="alt_mobile" value="<?= e($customer['alt_mobile'] ?? '') ?>" placeholder="Optional" inputmode="tel" autocomplete="tel" data-validate="mobile"></div>
      <?php if ($u['role'] === 'super_admin'): ?>
      <div class="form-group">
        <label>Allot to Admin *</label>
        <select name="admin_id" required>
          <option value="">-- select --</option>
          <?php foreach ($admins as $a): ?>
            <option value="<?= (int)$a['id'] ?>" <?= (isset($customer) && (int)$customer['admin_id'] === (int)$a['id']) ? 'selected' : '' ?>><?= e($a['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <?php endif; ?>
    </div>
    <div class="form-group"><label>Address</label><textarea name="address" rows="2"><?= e($customer['address'] ?? '') ?></textarea></div>
    <div class="form-group">
      <label>Photo</label>
      <?php if (!empty($customer['photo'])): ?>
        <div style="margin-bottom:8px"><img class="customer-photo" src="<?= e(base_url($customer['photo'])) ?>"></div>
      <?php endif; ?>
      <input type="file" name="photo" accept="image/*">
    </div>
    <button class="btn" type="submit"><?= $customer ? 'Save Changes' : 'Add Customer' ?></button>
    <a class="btn secondary" href="<?= e(base_url($u['role'] === 'super_admin' ? 'superadmin/customers.php' : 'admin/customers.php')) ?>">Cancel</a>
  </form>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
