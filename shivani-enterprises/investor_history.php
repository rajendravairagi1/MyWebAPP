<?php
require_once __DIR__ . '/includes/functions.php';
$u = require_login();
$pageTitle = 'Investor History';

$sql = "SELECT i.*,
  COALESCE((SELECT SUM(amount) FROM investor_transactions WHERE investor_id = i.id AND type = 'investment'), 0) AS total_invested,
  COALESCE((SELECT SUM(amount) FROM investor_transactions WHERE investor_id = i.id AND type = 'profit'), 0) AS total_profit,
  COALESCE((SELECT SUM(amount) FROM investor_transactions WHERE investor_id = i.id AND type = 'payment'), 0) AS total_paid,
  (SELECT MAX(txn_date) FROM investor_transactions WHERE investor_id = i.id) AS last_txn_date
  FROM investors i WHERE i.owner_id = ? ORDER BY last_txn_date DESC";
$stmt = db()->prepare($sql);
$stmt->execute([$u['id']]);
$all = $stmt->fetchAll();

$investors = [];
foreach ($all as $i) {
    $balance = (float)$i['total_invested'] + (float)$i['total_profit'] - (float)$i['total_paid'];
    if ((float)$i['total_invested'] > 0 && abs($balance) < 0.01) {
        $investors[] = $i;
    }
}

require __DIR__ . '/includes/header.php';
?>
<p><a href="<?= e(base_url('investors.php')) ?>">&larr; Back to investors</a></p>
<div class="card">
  <h3 class="mt-0">History <span class="badge gray">Fully Settled Investors</span></h3>
  <p class="text-muted" style="font-size:13px">Jin investors ka poora hisaab ho gaya hai (investment + profit = poora paid, balance 0) wo yaha aa jaate hai - purana record dekhne ke liye hamesha available rehta hai.</p>
  <table>
    <tr><th>Name</th><th>Mobile</th><th>Total Investment</th><th>Total Profit Credited</th><th>Total Paid</th><th>Settled On</th></tr>
    <?php foreach ($investors as $i): ?>
    <tr>
      <td><a href="<?= e(base_url('investor_view.php?id=' . $i['id'])) ?>"><strong><?= e($i['name']) ?></strong></a></td>
      <td><?= e($i['mobile']) ?></td>
      <td><?= money($i['total_invested']) ?></td>
      <td><?= money($i['total_profit']) ?></td>
      <td><?= money($i['total_paid']) ?></td>
      <td><?= $i['last_txn_date'] ? e(date('d-M-Y', strtotime($i['last_txn_date']))) : '-' ?></td>
    </tr>
    <?php endforeach; ?>
    <?php if (!$investors): ?><tr><td colspan="6" class="text-muted">Koi settled investor nahi hai abhi.</td></tr><?php endif; ?>
  </table>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
