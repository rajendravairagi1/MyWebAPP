<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/charts.php';
require_role('super_admin');
$pageTitle = 'Super Admin Dashboard';

$totalSales = (float)db()->query('SELECT COALESCE(SUM(total_amount),0) FROM sales')->fetchColumn();
$totalPaid = (float)db()->query('SELECT COALESCE(SUM(amount),0) FROM payments')->fetchColumn();
$totalCustomers = (int)db()->query('SELECT COUNT(*) FROM customers')->fetchColumn();
$totalAdmins = (int)db()->query("SELECT COUNT(*) FROM users WHERE role='admin'")->fetchColumn();
$balanceDue = $totalSales - $totalPaid;
$pendingFollowups = (int)db()->query("SELECT COUNT(*) FROM followups WHERE status='pending' AND follow_up_date <= CURDATE()")->fetchColumn();
$collectionPct = $totalSales > 0 ? min(100, ($totalPaid / $totalSales) * 100) : 0;

$topProducts = db()->query('
  SELECT si.product_name, SUM(si.qty) AS qty, SUM(si.line_total) AS amount
  FROM sale_items si GROUP BY si.product_name ORDER BY amount DESC LIMIT 8
')->fetchAll();

$adminWise = db()->query('
  SELECT u.id, u.name,
    COALESCE((SELECT SUM(s.total_amount) FROM sales s WHERE s.admin_id = u.id), 0) AS sold,
    COALESCE((SELECT SUM(p.amount) FROM payments p WHERE p.admin_id = u.id), 0) AS collected
  FROM users u WHERE u.role = "admin" ORDER BY sold DESC
')->fetchAll();

// Last 14 days sales trend, zero-filled for days with no sales.
$trendRows = db()->query('
  SELECT DATE(sale_date) d, SUM(total_amount) amt FROM sales
  WHERE sale_date >= CURDATE() - INTERVAL 13 DAY
  GROUP BY DATE(sale_date)
')->fetchAll(PDO::FETCH_KEY_PAIR);
$trendLabels = []; $trendValues = [];
for ($i = 13; $i >= 0; $i--) {
    $d = date('Y-m-d', strtotime("-$i day"));
    $trendLabels[] = date('d M', strtotime($d));
    $trendValues[] = (float)($trendRows[$d] ?? 0);
}

$topProdLabels = array_map(fn($p) => $p['product_name'], array_slice($topProducts, 0, 6));
$topProdValues = array_map(fn($p) => (float)$p['amount'], array_slice($topProducts, 0, 6));

require __DIR__ . '/../includes/header.php';
?>
<div class="stat-grid">
  <div class="stat-card has-icon"><div class="stat-icon c1">&#128176;</div><div><div class="label">Total Sales</div><div class="value"><?= money($totalSales) ?></div></div></div>
  <div class="stat-card has-icon"><div class="stat-icon c2">&#9989;</div><div><div class="label">Total Collected</div><div class="value"><?= money($totalPaid) ?></div></div></div>
  <div class="stat-card has-icon"><div class="stat-icon c3">&#9203;</div><div><div class="label">Balance Due</div><div class="value"><?= money($balanceDue) ?></div></div></div>
  <div class="stat-card has-icon"><div class="stat-icon c4">&#128101;</div><div><div class="label">Customers</div><div class="value"><?= $totalCustomers ?></div></div></div>
  <div class="stat-card"><div class="label">Admins</div><div class="value"><?= $totalAdmins ?></div></div>
  <div class="stat-card"><div class="label">Pending Follow-ups (due)</div><div class="value"><?= $pendingFollowups ?></div></div>
</div>

<div class="chart-row">
  <div class="card chart-card">
    <h3 class="mt-0">Sales Trend (last 14 days)</h3>
    <?= svg_area_chart($trendLabels, $trendValues, 600, 200, '#5eead4') ?>
  </div>
  <div class="card">
    <h3 class="mt-0">Collection Rate</h3>
    <div class="gauge-wrap">
      <?= svg_gauge($collectionPct, 220, '#5eead4') ?>
      <div class="gauge-value"><?= round($collectionPct) ?>%</div>
    </div>
    <p class="text-muted" style="text-align:center">of total sales collected so far</p>
  </div>
</div>

<div class="card chart-card">
  <h3 class="mt-0">Top Products by Sales Value</h3>
  <?= svg_bar_chart($topProdLabels, $topProdValues, 700, 220, '#f472b6') ?>
</div>

<div class="card">
  <h3 class="mt-0">Admin-wise Sales &amp; Collection</h3>
  <table>
    <tr><th>Admin</th><th>Sold</th><th>Collected</th><th>Balance Due</th><th></th></tr>
    <?php foreach ($adminWise as $a): ?>
    <tr>
      <td><?= e($a['name']) ?></td>
      <td><?= money($a['sold']) ?></td>
      <td><?= money($a['collected']) ?></td>
      <td><?= money($a['sold'] - $a['collected']) ?></td>
      <td><a href="<?= e(base_url('superadmin/reports_admin_wise.php?admin_id=' . $a['id'])) ?>">View details</a></td>
    </tr>
    <?php endforeach; ?>
  </table>
</div>

<div class="card">
  <h3 class="mt-0">Top Products (table)</h3>
  <table>
    <tr><th>Product</th><th>Qty Sold</th><th>Amount</th></tr>
    <?php foreach ($topProducts as $p): ?>
    <tr><td><?= e($p['product_name']) ?></td><td><?= rtrim(rtrim(number_format($p['qty'],2),'0'),'.') ?></td><td><?= money($p['amount']) ?></td></tr>
    <?php endforeach; ?>
  </table>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
