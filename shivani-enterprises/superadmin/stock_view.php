<?php
require_once __DIR__ . '/../includes/functions.php';
require_role('super_admin');
$pageTitle = 'Stock — All Godowns';

$rows = db()->query("
  SELECT p.id AS product_id, p.name AS product_name, p.unit,
         g.id AS godam_id, g.name AS godam_name, ps.qty
  FROM product_stock ps
  JOIN products p ON p.id = ps.product_id
  JOIN godams g  ON g.id = ps.godam_id
  WHERE g.is_active = 1
  ORDER BY p.name, g.name
")->fetchAll();

$grouped = [];
foreach ($rows as $r) {
    $grouped[$r['product_id']]['product'] = $r['product_name'] . ' (' . $r['unit'] . ')';
    $grouped[$r['product_id']]['lines'][] = ['godam' => $r['godam_name'], 'qty' => (float)$r['qty']];
    $grouped[$r['product_id']]['total'] = ($grouped[$r['product_id']]['total'] ?? 0) + (float)$r['qty'];
}

$movements = db()->query("
  SELECT sm.*, p.name AS product_name, g.name AS godam_name, u.name AS admin_name
  FROM stock_movements sm
  JOIN products p ON p.id = sm.product_id
  JOIN godams g  ON g.id = sm.godam_id
  LEFT JOIN users u ON u.id = sm.admin_id
  ORDER BY sm.created_at DESC, sm.id DESC LIMIT 100
")->fetchAll();

require __DIR__ . '/../includes/header.php';
?>
<p><a href="<?= e(base_url('superadmin/godams.php')) ?>">&larr; Back to Godowns</a>
&middot; <a href="<?= e(base_url('superadmin/stock_entry.php')) ?>">+ Add Stock Entry</a></p>

<div class="card">
  <h3 class="mt-0">Current Stock</h3>
  <?php if (!$grouped): ?>
    <p class="text-muted">Abhi tak koi stock entry nahi.</p>
  <?php else: ?>
    <table>
      <tr><th>Product</th><th>Godown-wise Stock</th><th>Total Qty</th></tr>
      <?php foreach ($grouped as $g): ?>
      <tr>
        <td><?= e($g['product']) ?></td>
        <td>
          <?php foreach ($g['lines'] as $ln): $low = $ln['qty'] < 5; ?>
            <div><?= e($ln['godam']) ?>: <strong style="color:<?= $low ? '#dc2626' : 'inherit' ?>"><?= rtrim(rtrim(number_format($ln['qty'], 2), '0'), '.') ?></strong><?= $low ? ' <span class="badge orange">low</span>' : '' ?></div>
          <?php endforeach; ?>
        </td>
        <td><strong><?= rtrim(rtrim(number_format($g['total'], 2), '0'), '.') ?></strong></td>
      </tr>
      <?php endforeach; ?>
    </table>
  <?php endif; ?>
</div>

<div class="card">
  <h3 class="mt-0">Recent Stock Movements (last 100)</h3>
  <?php if (!$movements): ?>
    <p class="text-muted">Abhi tak koi movement nahi.</p>
  <?php else: ?>
    <table>
      <tr><th>Date</th><th>Product</th><th>Godown</th><th>Type</th><th>Qty Change</th><th>Note</th><th>By</th></tr>
      <?php foreach ($movements as $m): ?>
      <tr>
        <td><?= e(date('d-M-Y', strtotime($m['created_at']))) ?></td>
        <td><?= e($m['product_name']) ?></td>
        <td><?= e($m['godam_name']) ?></td>
        <td><span class="badge <?= $m['movement_type'] === 'sale' ? 'orange' : ($m['movement_type'] === 'adjustment' ? 'gray' : 'green') ?>"><?= e($m['movement_type']) ?></span></td>
        <td style="color:<?= $m['qty_change'] < 0 ? '#dc2626' : '#16a34a' ?>"><?= ($m['qty_change'] > 0 ? '+' : '') . rtrim(rtrim(number_format((float)$m['qty_change'], 2), '0'), '.') ?></td>
        <td><?= e($m['note']) ?></td>
        <td><?= e($m['admin_name']) ?></td>
      </tr>
      <?php endforeach; ?>
    </table>
  <?php endif; ?>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
