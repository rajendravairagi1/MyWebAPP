<?php
require_once __DIR__ . '/../includes/functions.php';
$u = require_role('admin');
$pageTitle = 'My Sales / Invoices';

$search = trim($_GET['q'] ?? '');
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';

$where = ['s.admin_id = ?'];
$params = [$u['id']];
if ($search !== '') {
    $where[] = '(c.name LIKE ? OR c.mobile LIKE ? OR s.invoice_no LIKE ?)';
    $like = '%' . $search . '%';
    array_push($params, $like, $like, $like);
}
if ($from) { $where[] = 's.sale_date >= ?'; $params[] = $from; }
if ($to) { $where[] = 's.sale_date <= ?'; $params[] = $to; }
$whereSql = 'WHERE ' . implode(' AND ', $where);

$sql = "SELECT s.*, c.name AS customer_name, c.mobile
  FROM sales s JOIN customers c ON c.id = s.customer_id
  $whereSql ORDER BY s.sale_date DESC, s.id DESC LIMIT 300";
$stmt = db()->prepare($sql);
$stmt->execute($params);
$sales = $stmt->fetchAll();
$totalAmount = array_sum(array_column($sales, 'total_amount'));

require __DIR__ . '/../includes/header.php';
?>
<form class="filters card" method="get">
  <div class="form-group"><label>Search</label><input name="q" value="<?= e($search) ?>" placeholder="Customer / mobile / invoice"></div>
  <div class="form-group"><label>From</label><input type="date" name="from" value="<?= e($from) ?>"></div>
  <div class="form-group"><label>To</label><input type="date" name="to" value="<?= e($to) ?>"></div>
  <div class="form-group"><button class="btn" type="submit">Filter</button></div>
</form>

<div class="card">
  <p><strong>Total (filtered): <?= money($totalAmount) ?> across <?= count($sales) ?> invoices</strong></p>
  <table>
    <tr><th>Date</th><th>Invoice</th><th>Customer</th><th>Amount</th><th></th></tr>
    <?php foreach ($sales as $s): ?>
    <tr>
      <td><?= e(date('d-M-Y', strtotime($s['sale_date']))) ?></td>
      <td><?= e($s['invoice_no']) ?></td>
      <td><?= e($s['customer_name']) ?> (<?= e($s['mobile']) ?>)</td>
      <td><?= money($s['total_amount']) ?></td>
      <td><a href="<?= e(base_url('sale_view.php?id=' . $s['id'])) ?>">View</a></td>
    </tr>
    <?php endforeach; ?>
    <?php if (!$sales): ?><tr><td colspan="5" class="text-muted">No sales yet. Open a customer to create one.</td></tr><?php endif; ?>
  </table>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
