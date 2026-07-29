<?php
require_once __DIR__ . '/includes/functions.php';
$u = require_login();

$paymentId = resolve_id('payment');
$payment = null;
if ($paymentId) {
    $stmt = db()->prepare('SELECT * FROM payments WHERE id = ?');
    $stmt->execute([$paymentId]);
    $payment = $stmt->fetch();
    if (!$payment) { http_response_code(404); die('Payment not found.'); }
    $customerId = (int)$payment['customer_id'];
} else {
    $customerId = resolve_id_from('customer', 'customer_id');
}

$stmt = db()->prepare('SELECT * FROM customers WHERE id = ?');
$stmt->execute([$customerId]);
$customer = $stmt->fetch();
if (!$customer) { http_response_code(404); die('Customer not found.'); }
if ($u['role'] !== 'super_admin' && (int)$customer['admin_id'] !== (int)$u['id']) {
    http_response_code(403); die('Access denied.');
}
$pageTitle = $payment ? 'Edit Payment' : 'Record Payment';

// Only offer invoices that still need payment - a fully paid invoice has
// moved to History and shouldn't need (or accept) more payment against it.
// When editing an existing payment, its own amount is excluded from the
// "already paid" check (otherwise the very payment that completed an
// invoice would make that invoice disappear from its own edit form), and
// its currently-tagged invoice always stays in the list even if other
// payments have since fully covered it.
$salesStmt = db()->prepare(
    "SELECT s.id, s.invoice_no, s.total_amount, s.sale_date,
      COALESCE((SELECT SUM(p.amount) FROM payments p WHERE p.sale_id = s.id AND p.id != ?), 0) AS paid_for_sale
     FROM sales s WHERE s.customer_id = ? ORDER BY s.sale_date DESC"
);
$salesStmt->execute([$paymentId ?: 0, $customerId]);
$sales = array_filter($salesStmt->fetchAll(), function ($s) use ($payment) {
    $stillPending = (float)$s['paid_for_sale'] < (float)$s['total_amount'] - 0.01;
    $isCurrentlyTagged = $payment && (int)$payment['sale_id'] === (int)$s['id'];
    return $stillPending || $isCurrentlyTagged;
});
// Remaining balance per invoice, shown in the dropdown so it's clear at a
// glance how much is still due - prevents accidentally tagging a payment
// to the wrong invoice when two have similar totals.
foreach ($sales as &$s) {
    $s['remaining'] = max(0, (float)$s['total_amount'] - (float)$s['paid_for_sale']);
}
unset($s);

if ($payment && $_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    verify_csrf();
    db()->prepare('DELETE FROM payments WHERE id = ?')->execute([$paymentId]);
    flash('success', 'Payment deleted.');
    redirect('customer_view.php?c=' . sign_id('customer', $customerId));
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
        redirect('customer_view.php?c=' . sign_id('customer', $customerId));
    }
}

require __DIR__ . '/includes/header.php';
?>
<p><a href="<?= e(id_url('customer_view.php', 'customer', $customerId)) ?>">&larr; Back to <?= e($customer['name']) ?></a></p>
<div class="card">
  <h3 class="mt-0"><?= $payment ? 'Edit Payment' : 'Record Payment' ?> - <?= e($customer['name']) ?></h3>
  <?php foreach ($errors as $err): ?><div class="alert error"><?= e($err) ?></div><?php endforeach; ?>
  <?php $isDirect = $payment ? empty($payment['sale_id']) : true; ?>
  <div class="pay-mode-toggle" style="display:flex;gap:10px;margin-bottom:16px;flex-wrap:wrap">
    <button type="button" class="btn <?= $isDirect ? 'secondary' : '' ?>" id="modeInvoiceBtn" style="flex:1 1 200px">Against Invoice</button>
    <button type="button" class="btn <?= $isDirect ? '' : 'secondary' ?>" id="modeDirectBtn" style="flex:1 1 200px">Direct Payment</button>
  </div>
  <p class="text-muted" style="font-size:13px;margin-top:-8px" id="modeHint"></p>

  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="customer_id" value="<?= e(sign_id('customer', $customerId)) ?>">
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
    <div class="form-row" id="invoicePickerRow" style="<?= $isDirect ? 'display:none' : '' ?>">
      <div class="form-group">
        <label>Against Invoice</label>
        <select name="sale_id" id="saleIdSelect" data-remaining="<?= e(json_encode(array_column($sales, 'remaining', 'id'))) ?>">
          <option value="">-- select invoice --</option>
          <?php foreach ($sales as $s): ?>
            <option value="<?= (int)$s['id'] ?>" <?= (int)($payment['sale_id'] ?? 0) === (int)$s['id'] ? 'selected' : '' ?>><?= e($s['invoice_no']) ?> (Due <?= money($s['remaining']) ?> of <?= money($s['total_amount']) ?>, <?= e(date('d-M-Y', strtotime($s['sale_date']))) ?>)</option>
          <?php endforeach; ?>
        </select>
        <p class="text-muted" id="remainingHint" style="font-size:13px;margin:6px 0 0"></p>
      </div>
    </div>
    <div class="form-row">
      <div class="form-group"><label>Note (internal, PDF me nahi aayega)</label><input name="note" placeholder="Optional" value="<?= e($payment['note'] ?? '') ?>"></div>
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
<script>
(function () {
  var invBtn = document.getElementById('modeInvoiceBtn');
  var dirBtn = document.getElementById('modeDirectBtn');
  var pickerRow = document.getElementById('invoicePickerRow');
  var modeHint = document.getElementById('modeHint');
  var sel = document.getElementById('saleIdSelect');

  function setMode(direct) {
    if (direct) {
      pickerRow.style.display = 'none';
      if (sel) sel.value = '';
      invBtn.classList.add('secondary');
      dirBtn.classList.remove('secondary');
      modeHint.textContent = 'Direct payment — customer ke total balance mese seedha minus hoga (kisi specific invoice se tag nahi).';
    } else {
      pickerRow.style.display = '';
      invBtn.classList.remove('secondary');
      dirBtn.classList.add('secondary');
      modeHint.textContent = 'Kis invoice ke against payment aya hai wo select karein — us invoice ka balance kam hoga.';
    }
    if (hint) hint.textContent = '';
    if (sel) update();
  }
  invBtn.addEventListener('click', function () { setMode(false); });
  dirBtn.addEventListener('click', function () { setMode(true); });

  var amountInput = document.querySelector('input[name="amount"]');
  var hint = document.getElementById('remainingHint');
  // initialise hint copy for whichever mode is active on page load
  modeHint.textContent = <?= $isDirect ? "'Direct payment — customer ke total balance mese seedha minus hoga (kisi specific invoice se tag nahi).'" : "'Kis invoice ke against payment aya hai wo select karein — us invoice ka balance kam hoga.'" ?>;

  if (!sel || !amountInput || !hint) return;
  var remaining = {};
  try { remaining = JSON.parse(sel.dataset.remaining || '{}'); } catch (e) {}

  function fmt(n) {
    return n.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  function update() {
    var due = remaining[sel.value];
    if (due === undefined) { hint.textContent = ''; return; }
    var paid = parseFloat(amountInput.value) || 0;
    var left = Math.max(0, due - paid);
    if (paid <= 0) {
      hint.textContent = 'Is invoice par abhi bacha hua balance: Rs. ' + fmt(due);
    } else if (left <= 0.01) {
      hint.textContent = 'Ye payment invoice pura clear kar dega.';
    } else {
      hint.textContent = 'Is payment ke baad bhi Rs. ' + fmt(left) + ' bacha rahega (invoice Pending hi rahegi).';
    }
  }
  sel.addEventListener('change', update);
  amountInput.addEventListener('input', update);
  update();
})();
</script>
<?php require __DIR__ . '/includes/footer.php'; ?>
