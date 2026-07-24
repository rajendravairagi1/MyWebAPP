<?php
require_once __DIR__ . '/includes/functions.php';
$u = require_login();
$pageTitle = 'Add Follow-up';

$customerId = (int)($_GET['customer_id'] ?? $_POST['customer_id'] ?? 0);
$stmt = db()->prepare('SELECT * FROM customers WHERE id = ?');
$stmt->execute([$customerId]);
$customer = $stmt->fetch();
if (!$customer) { http_response_code(404); die('Customer not found.'); }
if ($u['role'] !== 'super_admin' && (int)$customer['admin_id'] !== (int)$u['id']) {
    http_response_code(403); die('Access denied.');
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $commitment = trim($_POST['commitment'] ?? '');
    $date = $_POST['follow_up_date'] ?? '';
    $time = trim($_POST['follow_up_time'] ?? '') ?: null;
    $remarks = trim($_POST['remarks'] ?? '');

    if ($date === '') $errors[] = 'Follow-up date is required.';

    if (!$errors) {
        $stmt = db()->prepare('INSERT INTO followups (customer_id, admin_id, commitment, follow_up_date, follow_up_time, remarks, created_by) VALUES (?,?,?,?,?,?,?)');
        $stmt->execute([$customerId, $customer['admin_id'], $commitment ?: null, $date, $time, $remarks ?: null, $u['id']]);
        flash('success', 'Follow-up added.');
        redirect('customer_view.php?id=' . $customerId);
    }
}

require __DIR__ . '/includes/header.php';
?>
<p><a href="<?= e(base_url('customer_view.php?id=' . $customerId)) ?>">&larr; Back to <?= e($customer['name']) ?></a></p>
<div class="card">
  <h3 class="mt-0">Add Follow-up / Commitment - <?= e($customer['name']) ?></h3>
  <?php foreach ($errors as $err): ?><div class="alert error"><?= e($err) ?></div><?php endforeach; ?>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="customer_id" value="<?= (int)$customerId ?>">
    <div class="form-group"><label>Commitment (what was promised / discussed)</label><textarea name="commitment" rows="2" placeholder="e.g. 3 coolers @ Rs.2500 promised next week"></textarea></div>
    <div class="form-row">
      <div class="form-group"><label>Follow-up Date *</label><input type="date" name="follow_up_date" required value="<?= date('Y-m-d') ?>"></div>
      <div class="form-group"><label>Time</label><input type="time" name="follow_up_time"></div>
    </div>
    <div class="form-group"><label>Remarks</label><input name="remarks" placeholder="Optional"></div>
    <button class="btn" type="submit">Save Follow-up</button>
  </form>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
