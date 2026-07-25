<?php
require_once __DIR__ . '/includes/functions.php';
$u = require_login();

$paymentId = (int)($_GET['id'] ?? 0);
$payment = null;
if ($paymentId) {
    $stmt = db()->prepare('SELECT * FROM payments WHERE id = ?');
    $stmt->execute([$paymentId]);
    $payment = $stmt->fetch();
    if (!$payment) { http_response_code(404); die('Payment not found.'); }
    $customerId = (int)$payment['customer_id'];
} else {
    $customerId = (int)($_GET['customer_id'] ?? $_POST['customer_id'] ?? 0);
}

$stmt = db()->prepare('SELECT * FROM customers WHERE id = ?');
$stmt->execute([$customerId]);
$customer = $stmt->fetch();
if (!$customer) { http_response_code(404); die('Customer not found.'); }
if ($u['role'] !== 'super_admin' && (int)$customer['admin_id'] !== (int)$u['id']) {
    http_response_code(403); die('Access denied.');
}
$pageTitle = $payment ? 'Edit Payment' : 'Record Payment';

$sales = db()->prepare('SELECT id, invoice_no, total_amount, sale_date FROM sales WHERE customer_id = ? ORDER BY sale_date DESC');
$sales->execute([$customerId]);
$sales = $sales->fetchAll();

if ($payment && $_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    verify_csrf();
    db()->prepare('DELETE FROM payments WHERE id = ?')->execute([$paymentId]);
    flash('success', 'Payment deleted.');
    redirect('customer_view.php?id=' . $customerId);
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? 'save') === 'save') {
    verify_csrf();
    $amount = (float)($_POST['amount'] ?? 0);
    $date = $_POST['payment_date'] ?? date('Y-m-d');
    $mode = $_POST['mode'] ?? 'cash';
    $note = trim($_POST['note'] ?? '');
    $saleId = (int)($_POST['sale_id'] ?? 0) ?: null;

    if ($amount <= 0) $errors[] = 'Amount must be greater than zero.';
    if (!in_array($mode, ['cash','upi','bank_transfer','cheque','other'], true)) $errors[] = 'Invalid payment mode.';

    if (!$errors) {
        if ($payment) {
            $stmt = db()->prepare('UPDATE payments SET sale_id=?, amount=?, payment_date=?, mode=?, note=? WHERE id=?');
            $stmt->execute([$saleId, $amount, $date, $mode, $note ?: null, $paymentId]);
            flash('success', 'Payment updated.');
        } else {
            $stmt = db()->prepare('INSERT INTO payments (customer_id, sale_id, admin_id, amount, payment_date, mode, note, created_by) VALUES (?,?,?,?,?,?,?,?)');
            $stmt->execute([$customerId, $saleId, $customer['admin_id'], $amount, $date, $mode, $note ?: null, $u['id']]);
            flash('success', 'Payment of ' . money($amount) . ' recorded.');
        }
        redirect('customer_view.php?id=' . $customerId);
    }
}

require __DIR__ . '/includes/header.php';
?>
<p><a href="<?= e(base_url('customer_view.php?id=' . $customerId)) ?>">&larr; Back to <?= e($customer['name']) ?></a></p>
<div class="card">
  <h3 class="mt-0"><?= $payment ? 'Edit Payment' : 'Record Payment' ?> - <?= e($customer['name']) ?></h3>
  <?php foreach ($errors as $err): ?><div class="alert error"><?= e($err) ?></div><?php endforeach; ?>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="customer_id" value="<?= (int)$customerId ?>">
    <div class="form-row">
      <div class="form-group"><label>Amount (Rs.) *</label><input type="number" step="0.01" min="0.01" name="amount" required value="<?= e($payment['amount'] ?? '') ?>"></div>
      <div class="form-group"><label>Payment Date</label><input type="date" name="payment_date" value="<?= e($payment['payment_date'] ?? date('Y-m-d')) ?>" required></div>
      <div class="form-group">
        <label>Mode</label>
        <select name="mode">
          <?php foreach (['cash'=>'Cash','upi'=>'UPI','bank_transfer'=>'Bank Transfer','cheque'=>'Cheque','other'=>'Other'] as $val => $label): ?>
            <option value="<?= $val ?>" <?= ($payment['mode'] ?? '') === $val ? 'selected' : '' ?>><?= $label ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label>Against Invoice (optional)</label>
        <select name="sale_id">
          <option value="">-- general payment --</option>
          <?php foreach ($sales as $s): ?>
            <option value="<?= (int)$s['id'] ?>" <?= (int)($payment['sale_id'] ?? 0) === (int)$s['id'] ? 'selected' : '' ?>><?= e($s['invoice_no']) ?> (<?= money($s['total_amount']) ?>, <?= e(date('d-M-Y', strtotime($s['sale_date']))) ?>)</option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group"><label>Note</label><input name="note" placeholder="Optional" value="<?= e($payment['note'] ?? '') ?>"></div>
    </div>
    <button class="btn" type="submit"><?= $payment ? 'Save Changes' : 'Save Payment' ?></button>
  </form>
  <?php if ($payment): ?>
    <form method="post" style="margin-top:10px" onsubmit="return confirm('Delete this payment entry? This cannot be undone.')">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="delete">
      <button class="btn small danger" type="submit">Delete Payment</button>
    </form>
  <?php endif; ?>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
