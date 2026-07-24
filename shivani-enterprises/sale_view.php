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

$pageTitle = 'Invoice ' . $sale['invoice_no'];
$waMsg = "Namaste " . $customer['name'] . ", aapka invoice " . $sale['invoice_no'] . " (Rs. " . number_format($sale['total_amount'], 2) . ") - PDF attach kar ke bhej rahe hain. Dhanyawad - " . get_setting('company_name', APP_NAME);

require __DIR__ . '/includes/header.php';
?>
<p><a href="<?= e(base_url('customer_view.php?id=' . $sale['customer_id'])) ?>">&larr; Back to <?= e($customer['name']) ?></a></p>
<div class="card">
  <h3 class="mt-0">Invoice <?= e($sale['invoice_no']) ?></h3>
  <p class="text-muted">
    Date: <?= e(date('d-M-Y', strtotime($sale['sale_date']))) ?> &middot;
    Customer: <?= e($customer['name']) ?> (<?= e($customer['mobile']) ?>) &middot;
    Admin: <?= e($sale['admin_name']) ?>
  </p>
  <table>
    <tr><th>Product</th><th>Qty</th><th>Price</th><th>Amount</th></tr>
    <?php foreach ($items as $it): ?>
    <tr>
      <td><?= e($it['product_name']) ?></td>
      <td><?= rtrim(rtrim(number_format($it['qty'],2),'0'),'.') ?></td>
      <td><?= money($it['price']) ?></td>
      <td><?= money($it['line_total']) ?></td>
    </tr>
    <?php endforeach; ?>
  </table>
  <p style="text-align:right;font-size:16px"><strong>Total: <?= money($sale['total_amount']) ?></strong></p>
  <?php if ($sale['notes']): ?><p class="text-muted">Note: <?= e($sale['notes']) ?></p><?php endif; ?>
  <p>
    <a class="btn small" href="<?= e(base_url('sale_view.php?id=' . $id . '&pdf=1')) ?>" target="_blank">Download PDF</a>
    <a class="btn small wa" target="_blank" href="<?= e(whatsapp_link($customer['mobile'], $waMsg)) ?>">Share on WhatsApp</a>
  </p>
  <p class="text-muted">Tip: WhatsApp does not allow auto-attaching a file via link. Click "Download PDF" first, then click "Share on WhatsApp" and manually attach the downloaded PDF in the chat.</p>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
