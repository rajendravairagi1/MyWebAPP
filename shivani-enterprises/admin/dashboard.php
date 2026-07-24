<?php
require_once __DIR__ . '/../includes/functions.php';
$u = require_role('admin');
$pageTitle = 'Dashboard';

$stmt = db()->prepare('SELECT COALESCE(SUM(total_amount),0) FROM sales WHERE admin_id = ?');
$stmt->execute([$u['id']]);
$totalSales = (float)$stmt->fetchColumn();

$stmt = db()->prepare('SELECT COALESCE(SUM(amount),0) FROM payments WHERE admin_id = ?');
$stmt->execute([$u['id']]);
$totalPaid = (float)$stmt->fetchColumn();

$stmt = db()->prepare('SELECT COUNT(*) FROM customers WHERE admin_id = ?');
$stmt->execute([$u['id']]);
$totalCustomers = (int)$stmt->fetchColumn();

$stmt = db()->prepare("SELECT COUNT(*) FROM followups WHERE admin_id = ? AND status='pending' AND follow_up_date <= CURDATE()");
$stmt->execute([$u['id']]);
$duefollowups = (int)$stmt->fetchColumn();

$stmt = db()->prepare('
  SELECT si.product_name, SUM(si.qty) AS qty, SUM(si.line_total) AS amount
  FROM sale_items si JOIN sales s ON s.id = si.sale_id
  WHERE s.admin_id = ? GROUP BY si.product_name ORDER BY amount DESC LIMIT 8
');
$stmt->execute([$u['id']]);
$topProducts = $stmt->fetchAll();

$stmt = db()->prepare("
  SELECT f.*, c.name AS customer_name, c.mobile FROM followups f
  JOIN customers c ON c.id = f.customer_id
  WHERE f.admin_id = ? AND f.status = 'pending' AND f.follow_up_date <= CURDATE()
  ORDER BY f.follow_up_date LIMIT 10
");
$stmt->execute([$u['id']]);
$todayFollowups = $stmt->fetchAll();

require __DIR__ . '/../includes/header.php';
?>
<div class="stat-grid">
  <div class="stat-card"><div class="label">Total Sold</div><div class="value"><?= money($totalSales) ?></div></div>
  <div class="stat-card"><div class="label">Total Collected</div><div class="value"><?= money($totalPaid) ?></div></div>
  <div class="stat-card"><div class="label">Balance Due</div><div class="value"><?= money($totalSales - $totalPaid) ?></div></div>
  <div class="stat-card"><div class="label">My Customers</div><div class="value"><?= $totalCustomers ?></div></div>
  <div class="stat-card"><div class="label">Follow-ups Due</div><div class="value"><?= $duefollowups ?></div></div>
</div>

<div class="card">
  <h3 class="mt-0">Follow-ups Due Today / Overdue</h3>
  <table>
    <tr><th>Date</th><th>Customer</th><th>Commitment</th><th></th></tr>
    <?php foreach ($todayFollowups as $f): ?>
    <tr>
      <td><?= e(date('d-M-Y', strtotime($f['follow_up_date']))) ?></td>
      <td><a href="<?= e(base_url('customer_view.php?id=' . $f['customer_id'])) ?>"><?= e($f['customer_name']) ?></a></td>
      <td><?= e($f['commitment']) ?></td>
      <td><a class="btn small wa" target="_blank" href="<?= e(whatsapp_link($f['mobile'], 'Namaste ' . $f['customer_name'] . ', payment/order follow-up ke liye sampark kar rahe hain. - ' . get_setting('company_name', APP_NAME))) ?>">WhatsApp</a></td>
    </tr>
    <?php endforeach; ?>
    <?php if (!$todayFollowups): ?><tr><td colspan="4" class="text-muted">No follow-ups due.</td></tr><?php endif; ?>
  </table>
</div>

<div class="card">
  <h3 class="mt-0">My Top Products</h3>
  <table>
    <tr><th>Product</th><th>Qty Sold</th><th>Amount</th></tr>
    <?php foreach ($topProducts as $p): ?>
    <tr><td><?= e($p['product_name']) ?></td><td><?= rtrim(rtrim(number_format($p['qty'],2),'0'),'.') ?></td><td><?= money($p['amount']) ?></td></tr>
    <?php endforeach; ?>
    <?php if (!$topProducts): ?><tr><td colspan="3" class="text-muted">No sales yet.</td></tr><?php endif; ?>
  </table>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
