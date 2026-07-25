<?php
require_once __DIR__ . '/includes/functions.php';
$u = require_login();
$pageTitle = 'Investors';

$search = trim($_GET['q'] ?? '');
$where = ['i.owner_id = ?'];
$params = [$u['id']];
if ($search !== '') {
    $where[] = '(i.name LIKE ? OR i.mobile LIKE ?)';
    $like = '%' . $search . '%';
    array_push($params, $like, $like);
}
$whereSql = 'WHERE ' . implode(' AND ', $where);

$sql = "SELECT i.*,
  COALESCE((SELECT SUM(amount) FROM investor_transactions WHERE investor_id = i.id AND type = 'investment'), 0) AS total_invested,
  COALESCE((SELECT SUM(amount) FROM investor_transactions WHERE investor_id = i.id AND type = 'payout'), 0) AS total_paid
  FROM investors i $whereSql ORDER BY i.status DESC, i.name";
$stmt = db()->prepare($sql);
$stmt->execute($params);
$investors = $stmt->fetchAll();

require __DIR__ . '/includes/header.php';
?>
<p><a class="btn" href="<?= e(base_url('investor_form.php')) ?>">+ Add Investor</a></p>

<form class="filters card" method="get">
  <div class="form-group search-group">
    <label>🔍 Search Investor</label>
    <input name="q" value="<?= e($search) ?>" placeholder="Naam ya mobile number se search karein" class="search-input">
  </div>
  <div class="form-group"><button class="btn" type="submit">Apply</button></div>
</form>

<div class="card">
  <table>
    <tr><th>Name</th><th>Mobile</th><th>Total Investment</th><th>Total Profit Paid</th><th>Net</th><th>Status</th></tr>
    <?php foreach ($investors as $i): $net = $i['total_invested'] - $i['total_paid']; ?>
    <tr>
      <td><a href="<?= e(base_url('investor_view.php?id=' . $i['id'])) ?>"><strong><?= e($i['name']) ?></strong></a></td>
      <td><?= e($i['mobile']) ?></td>
      <td><?= money($i['total_invested']) ?></td>
      <td><?= money($i['total_paid']) ?></td>
      <td><?= money($net) ?></td>
      <td><span class="badge <?= $i['status'] === 'active' ? 'green' : 'gray' ?>"><?= e($i['status']) ?></span></td>
    </tr>
    <?php endforeach; ?>
    <?php if (!$investors): ?><tr><td colspan="6" class="text-muted">No investors yet.</td></tr><?php endif; ?>
  </table>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
