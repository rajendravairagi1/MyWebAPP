<?php
require_once __DIR__ . '/../includes/functions.php';
$u = require_role('admin');
$pageTitle = 'My Reports';

$from = $_GET['from'] ?? date('Y-m-01');
$to = $_GET['to'] ?? date('Y-m-d');

$stmt = db()->prepare('SELECT COALESCE(SUM(total_amount),0) FROM sales WHERE admin_id = ? AND sale_date BETWEEN ? AND ?');
$stmt->execute([$u['id'], $from, $to]);
$periodSales = (float)$stmt->fetchColumn();

$stmt = db()->prepare('SELECT COALESCE(SUM(amount),0) FROM payments WHERE admin_id = ? AND payment_date BETWEEN ? AND ?');
$stmt->execute([$u['id'], $from, $to]);
$periodPaid = (float)$stmt->fetchColumn();

$stmt = db()->prepare('
  SELECT si.product_name, SUM(si.qty) AS qty, SUM(si.line_total) AS amount, COUNT(DISTINCT s.id) AS invoices
  FROM sale_items si JOIN sales s ON s.id = si.sale_id
  WHERE s.admin_id = ? AND s.sale_date BETWEEN ? AND ?
  GROUP BY si.product_name ORDER BY amount DESC
');
$stmt->execute([$u['id'], $from, $to]);
$productBreakdown = $stmt->fetchAll();

$stmt = db()->prepare('
  SELECT c.name, c.mobile,
    COALESCE((SELECT SUM(s.total_amount) FROM sales s WHERE s.customer_id = c.id), 0) AS total_given,
    COALESCE((SELECT SUM(p.amount) FROM payments p WHERE p.customer_id = c.id), 0) AS total_paid
  FROM customers c WHERE c.admin_id = ?
  HAVING (total_given - total_paid) > 0
  ORDER BY (total_given - total_paid) DESC LIMIT 20
');
$stmt->execute([$u['id']]);
$topDue = $stmt->fetchAll();

require __DIR__ . '/../includes/header.php';
?>
<form class="filters card" method="get">
  <div class="form-group"><label>From</label><input type="date" name="from" value="<?= e($from) ?>"></div>
  <div class="form-group"><label>To</label><input type="date" name="to" value="<?= e($to) ?>"></div>
  <div class="form-group"><button class="btn" type="submit">Apply</button></div>
</form>

<div class="stat-grid">
  <div class="stat-card"><div class="label">Sold (period)</div><div class="value"><?= money($periodSales) ?></div></div>
  <div class="stat-card"><div class="label">Collected (period)</div><div class="value"><?= money($periodPaid) ?></div></div>
</div>

<div class="card">
  <h3 class="mt-0">Product-wise Sales (period)</h3>
  <table>
    <tr><th>Product</th><th>Qty</th><th>Invoices</th><th>Amount</th></tr>
    <?php foreach ($productBreakdown as $p): ?>
    <tr><td><?= e($p['product_name']) ?></td><td><?= rtrim(rtrim(number_format($p['qty'],2),'0'),'.') ?></td><td><?= (int)$p['invoices'] ?></td><td><?= money($p['amount']) ?></td></tr>
    <?php endforeach; ?>
    <?php if (!$productBreakdown): ?><tr><td colspan="4" class="text-muted">No sales in this period.</td></tr><?php endif; ?>
  </table>
</div>

<div class="card">
  <h3 class="mt-0">Customers with Highest Balance Due (all time)</h3>
  <table>
    <tr><th>Customer</th><th>Mobile</th><th>Given</th><th>Paid</th><th>Balance</th></tr>
    <?php foreach ($topDue as $c): ?>
    <tr>
      <td><?= e($c['name']) ?></td>
      <td><?= e($c['mobile']) ?></td>
      <td><?= money($c['total_given']) ?></td>
      <td><?= money($c['total_paid']) ?></td>
      <td style="color:#dc2626"><?= money($c['total_given'] - $c['total_paid']) ?></td>
    </tr>
    <?php endforeach; ?>
    <?php if (!$topDue): ?><tr><td colspan="5" class="text-muted">No dues.</td></tr><?php endif; ?>
  </table>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
