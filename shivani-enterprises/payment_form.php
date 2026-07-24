<?php
require_once __DIR__ . '/includes/functions.php';
$u = require_login();
$pageTitle = 'Record Payment';

$customerId = (int)($_GET['customer_id'] ?? $_POST['customer_id'] ?? 0);
$stmt = db()->prepare('SELECT * FROM customers WHERE id = ?');
$stmt->execute([$customerId]);
$customer = $stmt->fetch();
if (!$customer) { http_response_code(404); die('Customer not found.'); }
if ($u['role'] !== 'super_admin' && (int)$customer['admin_id'] !== (int)$u['id']) {
    http_response_code(403); die('Access denied.');
}

$sales = db()->prepare('SELECT id, invoice_no, total_amount, sale_date FROM sales WHERE customer_id = ? ORDER BY sale_date DESC');
$sales->execute([$customerId]);
$sales = $sales->fetchAll();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $amount = (float)($_POST['amount'] ?? 0);
    $date = $_POST['payment_date'] ?? date('Y-m-d');
    $mode = $_POST['mode'] ?? 'cash';
    $note = trim($_POST['note'] ?? '');
    $saleId = (int)($_POST['sale_id'] ?? 0) ?: null;

    if ($amount <= 0) $errors[] = 'Amount must be greater than zero.';
    if (!in_array($mode, ['cash','upi','bank_transfer','cheque','other'], true)) $errors[] = 'Invalid payment mode.';

    if (!$errors) {
        $stmt = db()->prepare('INSERT INTO payments (customer_id, sale_id, admin_id, amount, payment_date, mode, note, created_by) VALUES (?,?,?,?,?,?,?,?)');
        $stmt->execute([$customerId, $saleId, $customer['admin_id'], $amount, $date, $mode, $note ?: null, $u['id']]);
        flash('success', 'Payment of ' . money($amount) . ' recorded.');
        redirect('customer_view.php?id=' . $customerId);
    }
}

require __DIR__ . '/includes/header.php';
?>
<p><a href="<?= e(base_url('customer_view.php?id=' . $customerId)) ?>">&larr; Back to <?= e($customer['name']) ?></a></p>
<div class="card">
  <h3 class="mt-0">Record Payment - <?= e($customer['name']) ?></h3>
  <?php foreach ($errors as $err): ?><div class="alert error"><?= e($err) ?></div><?php endforeach; ?>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="customer_id" value="<?= (int)$customerId ?>">
    <div class="form-row">
      <div class="form-group"><label>Amount (Rs.) *</label><input type="number" step="0.01" min="0.01" name="amount" required></div>
      <div class="form-group"><label>Payment Date</label><input type="date" name="payment_date" value="<?= date('Y-m-d') ?>" required></div>
      <div class="form-group">
        <label>Mode</label>
        <select name="mode">
          <option value="cash">Cash</option>
          <option value="upi">UPI</option>
          <option value="bank_transfer">Bank Transfer</option>
          <option value="cheque">Cheque</option>
          <option value="other">Other</option>
        </select>
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label>Against Invoice (optional)</label>
        <select name="sale_id">
          <option value="">-- general payment --</option>
          <?php foreach ($sales as $s): ?>
            <option value="<?= (int)$s['id'] ?>"><?= e($s['invoice_no']) ?> (<?= money($s['total_amount']) ?>, <?= e(date('d-M-Y', strtotime($s['sale_date']))) ?>)</option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group"><label>Note</label><input name="note" placeholder="Optional"></div>
    </div>
    <button class="btn" type="submit">Save Payment</button>
  </form>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
