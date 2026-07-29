<?php
require_once __DIR__ . '/../includes/functions.php';
require_role('super_admin');
$pageTitle = 'All Sales';

$adminFilter = (int)($_GET['admin_id'] ?? 0);
$productFilter = trim($_GET['product'] ?? '');
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';
$search = trim($_GET['q'] ?? '');

$where = [];
$params = [];
if ($adminFilter) { $where[] = 's.admin_id = ?'; $params[] = $adminFilter; }
if ($from) { $where[] = 's.sale_date >= ?'; $params[] = $from; }
if ($to) { $where[] = 's.sale_date <= ?'; $params[] = $to; }
if ($search !== '') {
    $where[] = '(c.name LIKE ? OR c.mobile LIKE ? OR s.invoice_no LIKE ?)';
    $like = '%' . $search . '%';
    array_push($params, $like, $like, $like);
}
if ($productFilter !== '') {
    $where[] = 's.id IN (SELECT sale_id FROM sale_items WHERE product_name LIKE ?)';
    $params[] = '%' . $productFilter . '%';
}
$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$sql = "SELECT s.*, c.name AS customer_name, c.mobile, u.name AS admin_name
  FROM sales s JOIN customers c ON c.id = s.customer_id JOIN users u ON u.id = s.admin_id
  $whereSql ORDER BY s.sale_date DESC, s.id DESC LIMIT 300";
$stmt = db()->prepare($sql);
$stmt->execute($params);
$sales = $stmt->fetchAll();
$totalAmount = array_sum(array_column($sales, 'total_amount'));

$admins = db()->query('SELECT id, name FROM users WHERE role = "admin" ORDER BY name')->fetchAll();
$products = db()->query('SELECT DISTINCT name FROM products ORDER BY name')->fetchAll();

require __DIR__ . '/../includes/header.php';
?>
<form class="filters card" method="get">
  <div class="form-group"><label>Search</label><input name="q" value="<?= e($search) ?>" placeholder="Customer / mobile / invoice"></div>
  <div class="form-group">
    <label>Admin</label>
    <select name="admin_id">
      <option value="0">All</option>
      <?php foreach ($admins as $a): ?><option value="<?= (int)$a['id'] ?>" <?= $adminFilter === (int)$a['id'] ? 'selected' : '' ?>><?= e($a['name']) ?></option><?php endforeach; ?>
    </select>
  </div>
  <div class="form-group">
    <label>Product</label>
    <select name="product">
      <option value="">All</option>
      <?php foreach ($products as $p): ?><option value="<?= e($p['name']) ?>" <?= $productFilter === $p['name'] ? 'selected' : '' ?>><?= e($p['name']) ?></option><?php endforeach; ?>
    </select>
  </div>
  <div class="form-group"><label>From</label><input type="date" name="from" value="<?= e($from) ?>"></div>
  <div class="form-group"><label>To</label><input type="date" name="to" value="<?= e($to) ?>"></div>
  <div class="form-group"><button class="btn" type="submit">Filter</button></div>
</form>

<div class="card">
  <p><strong>Total (filtered): <?= money($totalAmount) ?> across <?= count($sales) ?> invoices</strong></p>
  <table>
    <tr><th>Date</th><th>Invoice</th><th>Customer</th><th>Admin</th><th>Amount</th><th></th></tr>
    <?php foreach ($sales as $s): ?>
    <tr>
      <td><?= e(date('d-M-Y', strtotime($s['sale_date']))) ?></td>
      <td><?= e($s['invoice_no']) ?></td>
      <td><?= e($s['customer_name']) ?> (<?= e($s['mobile']) ?>)</td>
      <td><?= e($s['admin_name']) ?></td>
      <td><?= money($s['total_amount']) ?></td>
      <td><a href="<?= e(id_url('sale_view.php', 'sale', $s['id'])) ?>">View</a></td>
    </tr>
    <?php endforeach; ?>
    <?php if (!$sales): ?><tr><td colspan="6" class="text-muted">No sales found.</td></tr><?php endif; ?>
  </table>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
