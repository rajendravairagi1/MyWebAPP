<?php
require_once __DIR__ . '/../includes/functions.php';
require_role('super_admin');
$pageTitle = 'All Payments';

$adminFilter = (int)($_GET['admin_id'] ?? 0);
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';
$search = trim($_GET['q'] ?? '');

$where = [];
$params = [];
if ($adminFilter) { $where[] = 'p.admin_id = ?'; $params[] = $adminFilter; }
if ($from) { $where[] = 'p.payment_date >= ?'; $params[] = $from; }
if ($to) { $where[] = 'p.payment_date <= ?'; $params[] = $to; }
if ($search !== '') {
    $where[] = '(c.name LIKE ? OR c.mobile LIKE ?)';
    $like = '%' . $search . '%';
    array_push($params, $like, $like);
}
$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$sql = "SELECT p.*, c.name AS customer_name, c.mobile, u.name AS admin_name
  FROM payments p JOIN customers c ON c.id = p.customer_id JOIN users u ON u.id = p.admin_id
  $whereSql ORDER BY p.payment_date DESC, p.id DESC LIMIT 300";
$stmt = db()->prepare($sql);
$stmt->execute($params);
$payments = $stmt->fetchAll();
$totalAmount = array_sum(array_column($payments, 'amount'));

$admins = db()->query('SELECT id, name FROM users WHERE role = "admin" ORDER BY name')->fetchAll();

require __DIR__ . '/../includes/header.php';
?>
<form class="filters card" method="get">
  <div class="form-group"><label>Search</label><input name="q" value="<?= e($search) ?>" placeholder="Customer / mobile"></div>
  <div class="form-group">
    <label>Admin</label>
    <select name="admin_id">
      <option value="0">All</option>
      <?php foreach ($admins as $a): ?><option value="<?= (int)$a['id'] ?>" <?= $adminFilter === (int)$a['id'] ? 'selected' : '' ?>><?= e($a['name']) ?></option><?php endforeach; ?>
    </select>
  </div>
  <div class="form-group"><label>From</label><input type="date" name="from" value="<?= e($from) ?>"></div>
  <div class="form-group"><label>To</label><input type="date" name="to" value="<?= e($to) ?>"></div>
  <div class="form-group"><button class="btn" type="submit">Filter</button></div>
</form>

<div class="card">
  <p><strong>Total (filtered): <?= money($totalAmount) ?> across <?= count($payments) ?> payments</strong></p>
  <table>
    <tr><th>Date</th><th>Customer</th><th>Admin</th><th>Amount</th><th>Mode</th><th>Note</th></tr>
    <?php foreach ($payments as $p): ?>
    <tr>
      <td><?= e(date('d-M-Y', strtotime($p['payment_date']))) ?></td>
      <td><a href="<?= e(id_url('customer_view.php', 'customer', $p['customer_id'])) ?>"><?= e($p['customer_name']) ?></a> (<?= e($p['mobile']) ?>)</td>
      <td><?= e($p['admin_name']) ?></td>
      <td><?= money($p['amount']) ?></td>
      <td><?= e($p['mode']) ?></td>
      <td><?= e($p['note']) ?></td>
    </tr>
    <?php endforeach; ?>
    <?php if (!$payments): ?><tr><td colspan="6" class="text-muted">No payments found.</td></tr><?php endif; ?>
  </table>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
