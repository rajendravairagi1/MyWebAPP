<?php
require_once __DIR__ . '/../includes/functions.php';
require_role('super_admin');
$pageTitle = 'All Customers';

$search = trim($_GET['q'] ?? '');
$adminFilter = (int)($_GET['admin_id'] ?? 0);

$where = [];
$params = [];
if ($search !== '') {
    $where[] = '(c.name LIKE ? OR c.mobile LIKE ? OR c.shop_name LIKE ? OR c.place LIKE ?)';
    $like = '%' . $search . '%';
    array_push($params, $like, $like, $like, $like);
}
if ($adminFilter) {
    $where[] = 'c.admin_id = ?';
    $params[] = $adminFilter;
}
$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$sql = "SELECT c.*, u.name AS admin_name,
  COALESCE((SELECT SUM(s.total_amount) FROM sales s WHERE s.customer_id = c.id), 0) AS total_given,
  COALESCE((SELECT SUM(p.amount) FROM payments p WHERE p.customer_id = c.id), 0) AS total_paid
  FROM customers c JOIN users u ON u.id = c.admin_id
  $whereSql ORDER BY c.created_at DESC LIMIT 300";
$stmt = db()->prepare($sql);
$stmt->execute($params);
$customers = $stmt->fetchAll();

$admins = db()->query('SELECT id, name FROM users WHERE role = "admin" ORDER BY name')->fetchAll();

require __DIR__ . '/../includes/header.php';
?>
<p><a class="btn" href="<?= e(base_url('customer_form.php')) ?>">+ Add Customer</a></p>

<form class="filters card" method="get">
  <div class="form-group"><label>Search</label><input name="q" value="<?= e($search) ?>" placeholder="Name / mobile / shop / place"></div>
  <div class="form-group">
    <label>Admin</label>
    <select name="admin_id">
      <option value="0">All Admins</option>
      <?php foreach ($admins as $a): ?>
        <option value="<?= (int)$a['id'] ?>" <?= $adminFilter === (int)$a['id'] ? 'selected' : '' ?>><?= e($a['name']) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="form-group"><button class="btn" type="submit">Filter</button></div>
</form>

<div class="card">
  <table>
    <tr><th>Name</th><th>Mobile</th><th>Place</th><th>Admin</th><th>Given</th><th>Paid</th><th>Balance</th></tr>
    <?php foreach ($customers as $c): $bal = $c['total_given'] - $c['total_paid']; ?>
    <tr>
      <td><a href="<?= e(base_url('customer_view.php?id=' . $c['id'])) ?>"><strong><?= e($c['name']) ?></strong></a><?= $c['shop_name'] ? ' <span class="text-muted">('.e($c['shop_name']).')</span>' : '' ?></td>
      <td><?= e($c['mobile']) ?></td>
      <td><?= e($c['place']) ?></td>
      <td><?= e($c['admin_name']) ?></td>
      <td><?= money($c['total_given']) ?></td>
      <td><?= money($c['total_paid']) ?></td>
      <td style="color:<?= $bal > 0 ? '#dc2626' : '#16a34a' ?>"><?= money($bal) ?></td>
    </tr>
    <?php endforeach; ?>
    <?php if (!$customers): ?><tr><td colspan="7" class="text-muted">No customers found.</td></tr><?php endif; ?>
  </table>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
