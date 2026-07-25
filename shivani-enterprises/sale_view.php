<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/lib/invoice_pdf.php';
$u = require_login();

$id = (int)($_GET['id'] ?? 0);
$stmt = db()->prepare('SELECT s.*, u.name AS admin_name FROM sales s JOIN users u ON u.id = s.admin_id WHERE s.id = ?');
$stmt->execute([$id]);
$sale = $stmt->fetch();
if (!$sale) { http_response_code(404); die('Sale not found.'); }
if ($u['role'] !== 'super_admin' && (int)$sale['admin_id'] !== (int)$u['id']) {
    http_response_code(403); die('Access denied.');
}

$custStmt = db()->prepare('SELECT * FROM customers WHERE id = ?');
$custStmt->execute([$sale['customer_id']]);
$customer = $custStmt->fetch();

$itemsStmt = db()->prepare('SELECT * FROM sale_items WHERE sale_id = ?');
$itemsStmt->execute([$id]);
$items = $itemsStmt->fetchAll();

// Payments explicitly tagged against this invoice - shown so it's clear
// exactly which payments make up the "paid" total that moves an invoice
// into History (customer_view.php / customer_invoice_history.php use the
// same sale_id-tagged sum).
$payStmt = db()->prepare('SELECT * FROM payments WHERE sale_id = ? ORDER BY payment_date, id');
$payStmt->execute([$id]);
$taggedPayments = $payStmt->fetchAll();
$taggedTotal = array_sum(array_column($taggedPayments, 'amount'));

if (($_GET['pdf'] ?? '') === '1') {
    render_invoice_pdf($sale, $items, $customer);
    exit;
}

$pageTitle = 'Invoice ' . $sale['invoice_no'];
$pdfUrl = base_url('sale_view.php?id=' . $id . '&pdf=1');
$pdfFilename = 'invoice-' . $sale['invoice_no'] . '.pdf';
$waText = "Namaste " . $customer['name'] . ", aapka invoice " . $sale['invoice_no'] . " (Rs. " . number_format($sale['total_amount'], 2) . ") - " . get_setting('company_name', APP_NAME);

require __DIR__ . '/includes/header.php';
?>
<p><a href="<?= e(base_url('customer_view.php?id=' . $sale['customer_id'])) ?>">&larr; Back to <?= e($customer['name']) ?></a></p>

<div class="card invoice-sheet">
  <div class="invoice-topbar">
    <div>
      <div class="invoice-company"><?= e(get_setting('company_name', APP_NAME)) ?></div>
      <?php $companyLine = trim(get_setting('company_address', '') . '  ' . get_setting('company_phone', '')); ?>
      <?php if ($companyLine): ?><div class="text-muted" style="font-size:13px"><?= e($companyLine) ?></div><?php endif; ?>
    </div>
    <div class="invoice-badge">TAX INVOICE / CHALAN</div>
  </div>

  <div class="invoice-meta">
    <div>
      <div class="li-head">Invoice No</div>
      <div class="invoice-meta-value"><?= e($sale['invoice_no']) ?></div>
    </div>
    <div>
      <div class="li-head">Date</div>
      <div class="invoice-meta-value"><?= e(date('d-M-Y', strtotime($sale['sale_date']))) ?></div>
    </div>
    <div>
      <div class="li-head">Handled By</div>
      <div class="invoice-meta-value"><?= e($sale['admin_name']) ?></div>
    </div>
  </div>

  <div class="invoice-bill-to">
    <div class="li-head">Bill To</div>
    <div class="invoice-meta-value"><?= e($customer['name']) ?><?= $customer['shop_name'] ? ' ('.e($customer['shop_name']).')' : '' ?></div>
    <div class="text-muted" style="font-size:14px">
      <?= e($customer['place']) ?><?= $customer['place'] ? ' · ' : '' ?>Mobile: <?= e($customer['mobile']) ?>
    </div>
  </div>

  <table>
    <tr><th>Product</th><th>Qty</th><th>Price</th><th>Amount</th></tr>
    <?php foreach ($items as $it): ?>
    <tr>
      <td><?= e($it['product_name']) ?><?= $it['product_id'] === null ? ' <span class="badge gray">custom</span>' : '' ?></td>
      <td><?= rtrim(rtrim(number_format($it['qty'],2),'0'),'.') ?></td>
      <td><?= money($it['price']) ?></td>
      <td><?= money($it['line_total']) ?></td>
    </tr>
    <?php endforeach; ?>
  </table>

  <div class="invoice-total-box">
    <div class="invoice-total-row"><span>Total Amount</span><strong><?= money($sale['total_amount']) ?></strong></div>
  </div>

  <?php if ($sale['notes']): ?><p class="text-muted">Note: <?= e($sale['notes']) ?></p><?php endif; ?>
</div>

<div class="card">
  <h3 class="mt-0">Payments Tagged to This Invoice</h3>
  <p class="text-muted" style="font-size:13px">Yehi payments jodkar tay hota hai ki ye invoice Pending hai ya History (pura paid) me ja chuki hai. Agar koi payment yaha galti se dikh raha hai, use "Edit" karke uski invoice badal sakte ho ya "-- general payment --" kar sakte ho.</p>
  <table>
    <tr><th>Date</th><th>Amount</th><th>Mode</th><th>Note</th><th></th></tr>
    <?php foreach ($taggedPayments as $p): ?>
    <tr>
      <td><?= e(date('d-M-Y', strtotime($p['payment_date']))) ?></td>
      <td><?= money($p['amount']) ?></td>
      <td><?= e($p['mode']) ?></td>
      <td><?= e($p['note']) ?></td>
      <td><a href="<?= e(base_url('payment_form.php?id=' . $p['id'])) ?>">Edit</a></td>
    </tr>
    <?php endforeach; ?>
    <?php if (!$taggedPayments): ?><tr><td colspan="5" class="text-muted">Is invoice par abhi koi payment tag nahi hai.</td></tr><?php endif; ?>
  </table>
  <p style="margin-top:10px"><strong>Total Tagged Paid: <?= money($taggedTotal) ?></strong> of <?= money($sale['total_amount']) ?>
    <?php $due = max(0, (float)$sale['total_amount'] - (float)$taggedTotal); ?>
    <?php if ($due > 0.01): ?><span class="badge orange">Due <?= money($due) ?></span><?php else: ?><span class="badge green">Fully Paid</span><?php endif; ?>
  </p>

  <div class="invoice-actions">
    <a class="btn small" href="<?= e($pdfUrl) ?>" target="_blank">Download PDF</a>
    <button type="button" class="btn small wa"
      onclick="shareFileToWhatsApp('<?= e($pdfUrl) ?>', '<?= e($pdfFilename) ?>', '<?= e(addslashes($waText)) ?>', this)">
      Share PDF on WhatsApp
    </button>
    <a class="btn small secondary" href="<?= e(base_url('sale_form.php?id=' . $id)) ?>">Edit Invoice</a>
    <form method="post" action="<?= e(base_url('sale_form.php?id=' . $id)) ?>" onsubmit="return doubleConfirm('Delete invoice <?= e(addslashes($sale['invoice_no'])) ?> permanently?', 'Are you absolutely sure? This is PERMANENT and cannot be undone.')">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="delete">
      <button class="btn small danger" type="submit">Delete Invoice</button>
    </form>
  </div>
  <p class="text-muted" style="font-size:13px">Mobile par "Share PDF on WhatsApp" dabate hi PDF taiyar hoke share-menu khulega — wahan WhatsApp choose karke jisko chahe usko bhej sakte ho, alag se download nahi karna padega. (Desktop/purane browser par PDF download hokar WhatsApp chat khulega, wahan file manually attach karni hogi.)</p>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
