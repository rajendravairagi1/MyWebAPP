<?php
require_once __DIR__ . '/../includes/functions.php';
require_role('super_admin');
$pageTitle = 'Global Ledger';

$adminFilter = (int)($_GET['admin_id'] ?? 0);
$onlyDue = !empty($_GET['only_due']);
$search = trim($_GET['q'] ?? '');

$where = [];
$params = [];
if ($adminFilter) { $where[] = 'c.admin_id = ?'; $params[] = $adminFilter; }
if ($search !== '') {
    $where[] = '(c.name LIKE ? OR c.mobile LIKE ?)';
    $like = '%' . $search . '%';
    array_push($params, $like, $like);
}
$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$sql = "SELECT c.id, c.name, c.mobile, c.place, u.name AS admin_name,
  COALESCE((SELECT SUM(s.total_amount) FROM sales s WHERE s.customer_id = c.id), 0) AS total_given,
  COALESCE((SELECT SUM(p.amount) FROM payments p WHERE p.customer_id = c.id), 0) AS total_paid
  FROM customers c JOIN users u ON u.id = c.admin_id
  $whereSql ORDER BY c.name LIMIT 500";
$stmt = db()->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

if ($onlyDue) {
    $rows = array_values(array_filter($rows, fn($r) => ($r['total_given'] - $r['total_paid']) > 0));
}

$grandGiven = array_sum(array_column($rows, 'total_given'));
$grandPaid = array_sum(array_column($rows, 'total_paid'));

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
  <div class="form-group">
    <label><input type="checkbox" name="only_due" value="1" <?= $onlyDue ? 'checked' : '' ?>> Only balance due</label>
  </div>
  <div class="form-group"><button class="btn" type="submit">Filter</button></div>
</form>

<div class="stat-grid">
  <div class="stat-card"><div class="label">Total Given</div><div class="value"><?= money($grandGiven) ?></div></div>
  <div class="stat-card"><div class="label">Total Paid</div><div class="value"><?= money($grandPaid) ?></div></div>
  <div class="stat-card"><div class="label">Total Balance Due</div><div class="value"><?= money($grandGiven - $grandPaid) ?></div></div>
</div>

<div class="card">
  <table>
    <tr><th>Customer</th><th>Mobile</th><th>Place</th><th>Admin</th><th>Given</th><th>Paid</th><th>Balance</th></tr>
    <?php foreach ($rows as $r): $bal = $r['total_given'] - $r['total_paid']; ?>
    <tr>
      <td><a href="<?= e(base_url('customer_view.php?id=' . $r['id'])) ?>"><strong><?= e($r['name']) ?></strong></a></td>
      <td><?= e($r['mobile']) ?></td>
      <td><?= e($r['place']) ?></td>
      <td><?= e($r['admin_name']) ?></td>
      <td><?= money($r['total_given']) ?></td>
      <td><?= money($r['total_paid']) ?></td>
      <td style="color:<?= $bal > 0 ? '#dc2626' : '#16a34a' ?>"><?= money($bal) ?></td>
    </tr>
    <?php endforeach; ?>
    <?php if (!$rows): ?><tr><td colspan="7" class="text-muted">No records.</td></tr><?php endif; ?>
  </table>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
