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

if (($_GET['pdf'] ?? '') === '1') {
    render_invoice_pdf($sale, $items, $customer);
    exit;
}

$totalCost = 0;
foreach ($items as $it) { $totalCost += (float)$it['qty'] * (float)$it['cost_price']; }
$totalProfit = (float)$sale['total_amount'] - $totalCost;

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

  <div class="invoice-actions">
    <a class="btn small" href="<?= e($pdfUrl) ?>" target="_blank">Download PDF</a>
    <button type="button" class="btn small wa"
      onclick="shareFileToWhatsApp('<?= e($pdfUrl) ?>', '<?= e($pdfFilename) ?>', '<?= e(addslashes($waText)) ?>', this)">
      Share PDF on WhatsApp
    </button>
  </div>
  <p class="text-muted" style="font-size:13px">Mobile par "Share PDF on WhatsApp" dabate hi PDF taiyar hoke share-menu khulega — wahan WhatsApp choose karke jisko chahe usko bhej sakte ho, alag se download nahi karna padega. (Desktop/purane browser par PDF download hokar WhatsApp chat khulega, wahan file manually attach karni hogi.)</p>
</div>

<div class="card">
  <h3 class="mt-0">Internal: Cost &amp; Profit <span class="badge gray">not on invoice</span></h3>
  <p class="text-muted" style="font-size:13px">Ye sirf aapko (admin/super admin) dikhta hai — customer invoice ya PDF me kabhi nahi jaata.</p>
  <table>
    <tr><th>Product</th><th>Qty</th><th>Cost/unit</th><th>Total Cost</th><th>Profit</th></tr>
    <?php foreach ($items as $it): $lineCost = (float)$it['qty'] * (float)$it['cost_price']; $lineProfit = (float)$it['line_total'] - $lineCost; ?>
    <tr>
      <td><?= e($it['product_name']) ?></td>
      <td><?= rtrim(rtrim(number_format($it['qty'],2),'0'),'.') ?></td>
      <td><?= money($it['cost_price']) ?></td>
      <td><?= money($lineCost) ?></td>
      <td style="color:<?= $lineProfit >= 0 ? '#16a34a' : '#dc2626' ?>"><?= money($lineProfit) ?></td>
    </tr>
    <?php endforeach; ?>
  </table>
  <div class="stat-grid" style="margin-top:14px">
    <div class="stat-card"><div class="label">Total Sold</div><div class="value"><?= money($sale['total_amount']) ?></div></div>
    <div class="stat-card"><div class="label">Total Cost</div><div class="value"><?= money($totalCost) ?></div></div>
    <div class="stat-card"><div class="label">Profit</div><div class="value" style="color:<?= $totalProfit >= 0 ? '#16a34a' : '#dc2626' ?>"><?= money($totalProfit) ?></div></div>
  </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
