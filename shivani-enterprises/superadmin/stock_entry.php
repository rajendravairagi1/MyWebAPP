<?php
require_once __DIR__ . '/../includes/functions.php';
$u = require_role('super_admin');
$pageTitle = 'Add Stock (Opening / Purchase)';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $productId = (int)($_POST['product_id'] ?? 0);
    $godamId = (int)($_POST['godam_id'] ?? 0);
    $qty = (float)($_POST['qty'] ?? 0);
    $type = $_POST['movement_type'] ?? 'purchase';
    $note = trim($_POST['note'] ?? '');

    if (!in_array($type, ['opening', 'purchase', 'adjustment'], true)) {
        $errors[] = 'Invalid stock entry type.';
    }
    if ($productId <= 0) $errors[] = 'Product select karein.';
    if ($godamId <= 0)   $errors[] = 'Godown select karein.';
    if ($qty == 0)       $errors[] = 'Qty zero nahi ho sakti (adjustment ke liye negative bhi de sakte ho).';

    if (!$errors) {
        $db = db();
        $db->beginTransaction();
        try {
            $db->prepare('INSERT INTO product_stock (product_id, godam_id, qty) VALUES (?,?,?) ON DUPLICATE KEY UPDATE qty = qty + VALUES(qty)')
               ->execute([$productId, $godamId, $qty]);
            $db->prepare('INSERT INTO stock_movements (product_id, godam_id, movement_type, qty_change, admin_id, note) VALUES (?,?,?,?,?,?)')
               ->execute([$productId, $godamId, $type, $qty, $u['id'], $note ?: null]);
            $db->commit();
            flash('success', 'Stock updated.');
            redirect('superadmin/stock_view.php');
        } catch (Throwable $e) {
            $db->rollBack();
            $errors[] = 'DB error: ' . $e->getMessage();
        }
    }
}

$products = db()->query('SELECT id, name, unit FROM products WHERE is_active = 1 ORDER BY name')->fetchAll();
$godams = db()->query('SELECT id, name FROM godams WHERE is_active = 1 ORDER BY name')->fetchAll();

require __DIR__ . '/../includes/header.php';
?>
<p><a href="<?= e(base_url('superadmin/godams.php')) ?>">&larr; Back to Godowns</a></p>
<div class="card">
  <h3 class="mt-0">Add Stock Entry</h3>
  <?php foreach ($errors as $err): ?><div class="alert error"><?= e($err) ?></div><?php endforeach; ?>
  <?php if (!$godams): ?>
    <div class="alert error">Pehle ek Godown add karein — <a href="<?= e(base_url('superadmin/godams.php')) ?>">Godowns page</a>.</div>
  <?php elseif (!$products): ?>
    <div class="alert error">Pehle Products add karein.</div>
  <?php else: ?>
  <form method="post">
    <?= csrf_field() ?>
    <div class="form-row">
      <div class="form-group">
        <label>Type</label>
        <select name="movement_type">
          <option value="purchase">Purchase (nayi khareed)</option>
          <option value="opening">Opening Stock (pehla balance)</option>
          <option value="adjustment">Adjustment (correction, +/-)</option>
        </select>
      </div>
      <div class="form-group">
        <label>Godown *</label>
        <select name="godam_id" required>
          <?php foreach ($godams as $g): ?><option value="<?= (int)$g['id'] ?>"><?= e($g['name']) ?></option><?php endforeach; ?>
        </select>
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label>Product *</label>
        <select name="product_id" required>
          <?php foreach ($products as $p): ?><option value="<?= (int)$p['id'] ?>"><?= e($p['name']) ?> (<?= e($p['unit']) ?>)</option><?php endforeach; ?>
        </select>
      </div>
      <div class="form-group"><label>Qty *</label><input type="number" step="0.01" name="qty" required placeholder="e.g. 10 (adjustment ke liye -5 bhi)"></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label>Note (optional)</label><input name="note" placeholder="e.g. Invoice #XYZ se khareeda"></div>
    </div>
    <button class="btn" type="submit">Save Stock Entry</button>
  </form>
  <?php endif; ?>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
