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

// An investor is "settled" (fully paid off - moves to History) once
// they've actually invested something AND their running balance
// (investment + profit credited - payment paid) is back to zero.
$sql = "SELECT i.*,
  COALESCE((SELECT SUM(amount) FROM investor_transactions WHERE investor_id = i.id AND type = 'investment'), 0) AS total_invested,
  COALESCE((SELECT SUM(amount) FROM investor_transactions WHERE investor_id = i.id AND type = 'profit'), 0) AS total_profit,
  COALESCE((SELECT SUM(amount) FROM investor_transactions WHERE investor_id = i.id AND type = 'payment'), 0) AS total_paid
  FROM investors i $whereSql ORDER BY i.name";
$stmt = db()->prepare($sql);
$stmt->execute($params);
$all = $stmt->fetchAll();

$investors = [];
$settledCount = 0;
foreach ($all as $i) {
    $balance = (float)$i['total_invested'] + (float)$i['total_profit'] - (float)$i['total_paid'];
    if ((float)$i['total_invested'] > 0 && abs($balance) < 0.01) {
        $settledCount++;
        continue; // fully settled - lives in investor_history.php instead
    }
    $investors[] = $i;
}

require __DIR__ . '/includes/header.php';
?>
<p>
  <a class="btn" href="<?= e(base_url('investor_form.php')) ?>">+ Add Investor</a>
  <a class="btn small secondary" href="<?= e(base_url('investor_history.php')) ?>">History (Settled Investors)<?= $settledCount ? ' (' . $settledCount . ')' : '' ?></a>
</p>

<form class="filters card" method="get">
  <div class="form-group search-group">
    <label>🔍 Search Investor</label>
    <input name="q" value="<?= e($search) ?>" placeholder="Naam ya mobile number se search karein" class="search-input">
  </div>
  <div class="form-group"><button class="btn" type="submit">Apply</button></div>
</form>

<div class="card">
  <table>
    <tr><th>Name</th><th>Mobile</th><th>Total Investment</th><th>Total Profit Credited</th><th>Total Paid</th><th>Balance</th></tr>
    <?php foreach ($investors as $i): $balance = (float)$i['total_invested'] + (float)$i['total_profit'] - (float)$i['total_paid']; ?>
    <tr>
      <td><a href="<?= e(id_url('investor_view.php', 'investor', $i['id'])) ?>"><strong><?= e($i['name']) ?></strong></a></td>
      <td><?= e($i['mobile']) ?></td>
      <td><?= money($i['total_invested']) ?></td>
      <td><?= money($i['total_profit']) ?></td>
      <td><?= money($i['total_paid']) ?></td>
      <td><?= money($balance) ?></td>
    </tr>
    <?php endforeach; ?>
    <?php if (!$investors): ?><tr><td colspan="6" class="text-muted">No active investors.</td></tr><?php endif; ?>
  </table>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
