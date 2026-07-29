<?php
require_once __DIR__ . '/../includes/functions.php';
require_role('super_admin');
$pageTitle = 'Admin-wise Report';

$adminId = (int)($_GET['admin_id'] ?? 0);
$admins = db()->query('SELECT id, name FROM users WHERE role = "admin" ORDER BY name')->fetchAll();

$selected = null;
$productBreakdown = [];
$customers = [];
$stats = ['sold' => 0, 'collected' => 0, 'customers' => 0, 'invoices' => 0];

if ($adminId) {
    foreach ($admins as $a) { if ((int)$a['id'] === $adminId) { $selected = $a; break; } }
    if ($selected) {
        $stmt = db()->prepare('SELECT COALESCE(SUM(total_amount),0) FROM sales WHERE admin_id = ?');
        $stmt->execute([$adminId]);
        $stats['sold'] = (float)$stmt->fetchColumn();

        $stmt = db()->prepare('SELECT COALESCE(SUM(amount),0) FROM payments WHERE admin_id = ?');
        $stmt->execute([$adminId]);
        $stats['collected'] = (float)$stmt->fetchColumn();

        $stmt = db()->prepare('SELECT COUNT(*) FROM customers WHERE admin_id = ?');
        $stmt->execute([$adminId]);
        $stats['customers'] = (int)$stmt->fetchColumn();

        $stmt = db()->prepare('SELECT COUNT(*) FROM sales WHERE admin_id = ?');
        $stmt->execute([$adminId]);
        $stats['invoices'] = (int)$stmt->fetchColumn();

        $stmt = db()->prepare('
          SELECT si.product_name, SUM(si.qty) AS qty, SUM(si.line_total) AS amount
          FROM sale_items si JOIN sales s ON s.id = si.sale_id
          WHERE s.admin_id = ? GROUP BY si.product_name ORDER BY amount DESC
        ');
        $stmt->execute([$adminId]);
        $productBreakdown = $stmt->fetchAll();

        $stmt = db()->prepare('
          SELECT c.id, c.name, c.mobile,
            COALESCE((SELECT SUM(s.total_amount) FROM sales s WHERE s.customer_id = c.id), 0) AS total_given,
            COALESCE((SELECT SUM(p.amount) FROM payments p WHERE p.customer_id = c.id), 0) AS total_paid
          FROM customers c WHERE c.admin_id = ? ORDER BY c.name
        ');
        $stmt->execute([$adminId]);
        $customers = $stmt->fetchAll();
    }
}

require __DIR__ . '/../includes/header.php';
?>
<form class="filters card" method="get">
  <div class="form-group">
    <label>Select Admin</label>
    <select name="admin_id" onchange="this.form.submit()">
      <option value="0">-- select an admin --</option>
      <?php foreach ($admins as $a): ?>
        <option value="<?= (int)$a['id'] ?>" <?= $adminId === (int)$a['id'] ? 'selected' : '' ?>><?= e($a['name']) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
</form>

<?php if ($selected): ?>
<div class="stat-grid">
  <div class="stat-card"><div class="label">Total Sold</div><div class="value"><?= money($stats['sold']) ?></div></div>
  <div class="stat-card"><div class="label">Total Collected</div><div class="value"><?= money($stats['collected']) ?></div></div>
  <div class="stat-card"><div class="label">Balance Due</div><div class="value"><?= money($stats['sold'] - $stats['collected']) ?></div></div>
  <div class="stat-card"><div class="label">Customers</div><div class="value"><?= $stats['customers'] ?></div></div>
  <div class="stat-card"><div class="label">Invoices</div><div class="value"><?= $stats['invoices'] ?></div></div>
</div>

<div class="card">
  <h3 class="mt-0">Product-wise Sales for <?= e($selected['name']) ?></h3>
  <table>
    <tr><th>Product</th><th>Qty Sold</th><th>Amount</th></tr>
    <?php foreach ($productBreakdown as $p): ?>
    <tr><td><?= e($p['product_name']) ?></td><td><?= rtrim(rtrim(number_format($p['qty'],2),'0'),'.') ?></td><td><?= money($p['amount']) ?></td></tr>
    <?php endforeach; ?>
    <?php if (!$productBreakdown): ?><tr><td colspan="3" class="text-muted">No sales yet.</td></tr><?php endif; ?>
  </table>
</div>

<div class="card">
  <h3 class="mt-0">Customers of <?= e($selected['name']) ?></h3>
  <table>
    <tr><th>Customer</th><th>Mobile</th><th>Given</th><th>Paid</th><th>Balance</th></tr>
    <?php foreach ($customers as $c): $bal = $c['total_given'] - $c['total_paid']; ?>
    <tr>
      <td><a href="<?= e(id_url('customer_view.php', 'customer', $c['id'])) ?>"><strong><?= e($c['name']) ?></strong></a></td>
      <td><?= e($c['mobile']) ?></td>
      <td><?= money($c['total_given']) ?></td>
      <td><?= money($c['total_paid']) ?></td>
      <td style="color:<?= $bal > 0 ? '#dc2626' : '#16a34a' ?>"><?= money($bal) ?></td>
    </tr>
    <?php endforeach; ?>
  </table>
</div>
<?php endif; ?>
<?php require __DIR__ . '/../includes/footer.php'; ?>
