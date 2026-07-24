<?php
require_once __DIR__ . '/../includes/functions.php';
$u = require_role('admin');
$pageTitle = 'My Customers';

$search = trim($_GET['q'] ?? '');
$where = ['c.admin_id = ?'];
$params = [$u['id']];
if ($search !== '') {
    $where[] = '(c.name LIKE ? OR c.mobile LIKE ? OR c.shop_name LIKE ? OR c.place LIKE ?)';
    $like = '%' . $search . '%';
    array_push($params, $like, $like, $like, $like);
}
$whereSql = 'WHERE ' . implode(' AND ', $where);

$sql = "SELECT c.*,
  COALESCE((SELECT SUM(s.total_amount) FROM sales s WHERE s.customer_id = c.id), 0) AS total_given,
  COALESCE((SELECT SUM(p.amount) FROM payments p WHERE p.customer_id = c.id), 0) AS total_paid
  FROM customers c $whereSql ORDER BY c.created_at DESC LIMIT 300";
$stmt = db()->prepare($sql);
$stmt->execute($params);
$customers = $stmt->fetchAll();

require __DIR__ . '/../includes/header.php';
?>
<p><a class="btn" href="<?= e(base_url('customer_form.php')) ?>">+ Add Customer</a></p>

<form class="filters card" method="get">
  <div class="form-group"><label>Search</label><input name="q" value="<?= e($search) ?>" placeholder="Name / mobile / shop / place"></div>
  <div class="form-group"><button class="btn" type="submit">Filter</button></div>
</form>

<div class="card">
  <table>
    <tr><th>Name</th><th>Mobile</th><th>Place</th><th>Given</th><th>Paid</th><th>Balance</th></tr>
    <?php foreach ($customers as $c): $bal = $c['total_given'] - $c['total_paid']; ?>
    <tr>
      <td><a href="<?= e(base_url('customer_view.php?id=' . $c['id'])) ?>"><strong><?= e($c['name']) ?></strong></a><?= $c['shop_name'] ? ' <span class="text-muted">('.e($c['shop_name']).')</span>' : '' ?></td>
      <td><?= e($c['mobile']) ?></td>
      <td><?= e($c['place']) ?></td>
      <td><?= money($c['total_given']) ?></td>
      <td><?= money($c['total_paid']) ?></td>
      <td style="color:<?= $bal > 0 ? '#dc2626' : '#16a34a' ?>"><?= money($bal) ?></td>
    </tr>
    <?php endforeach; ?>
    <?php if (!$customers): ?><tr><td colspan="6" class="text-muted">No customers yet.</td></tr><?php endif; ?>
  </table>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
