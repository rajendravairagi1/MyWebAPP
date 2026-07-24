<?php
require_once __DIR__ . '/../includes/functions.php';
$u = require_role('admin');
$pageTitle = 'My Payments';

$search = trim($_GET['q'] ?? '');
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';

$where = ['p.admin_id = ?'];
$params = [$u['id']];
if ($search !== '') {
    $where[] = '(c.name LIKE ? OR c.mobile LIKE ?)';
    $like = '%' . $search . '%';
    array_push($params, $like, $like);
}
if ($from) { $where[] = 'p.payment_date >= ?'; $params[] = $from; }
if ($to) { $where[] = 'p.payment_date <= ?'; $params[] = $to; }
$whereSql = 'WHERE ' . implode(' AND ', $where);

$sql = "SELECT p.*, c.name AS customer_name, c.mobile
  FROM payments p JOIN customers c ON c.id = p.customer_id
  $whereSql ORDER BY p.payment_date DESC, p.id DESC LIMIT 300";
$stmt = db()->prepare($sql);
$stmt->execute($params);
$payments = $stmt->fetchAll();
$totalAmount = array_sum(array_column($payments, 'amount'));

require __DIR__ . '/../includes/header.php';
?>
<form class="filters card" method="get">
  <div class="form-group"><label>Search</label><input name="q" value="<?= e($search) ?>" placeholder="Customer / mobile"></div>
  <div class="form-group"><label>From</label><input type="date" name="from" value="<?= e($from) ?>"></div>
  <div class="form-group"><label>To</label><input type="date" name="to" value="<?= e($to) ?>"></div>
  <div class="form-group"><button class="btn" type="submit">Filter</button></div>
</form>

<div class="card">
  <p><strong>Total (filtered): <?= money($totalAmount) ?> across <?= count($payments) ?> payments</strong></p>
  <table>
    <tr><th>Date</th><th>Customer</th><th>Amount</th><th>Mode</th><th>Note</th></tr>
    <?php foreach ($payments as $p): ?>
    <tr>
      <td><?= e(date('d-M-Y', strtotime($p['payment_date']))) ?></td>
      <td><a href="<?= e(base_url('customer_view.php?id=' . $p['customer_id'])) ?>"><?= e($p['customer_name']) ?></a> (<?= e($p['mobile']) ?>)</td>
      <td><?= money($p['amount']) ?></td>
      <td><?= e($p['mode']) ?></td>
      <td><?= e($p['note']) ?></td>
    </tr>
    <?php endforeach; ?>
    <?php if (!$payments): ?><tr><td colspan="5" class="text-muted">No payments yet.</td></tr><?php endif; ?>
  </table>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
