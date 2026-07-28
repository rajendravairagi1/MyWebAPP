<?php
require_once __DIR__ . '/../includes/functions.php';
require_role('super_admin');
$pageTitle = 'Godams / Warehouses';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';
    if ($action === 'create') {
        $name = trim($_POST['name'] ?? '');
        $address = trim($_POST['address'] ?? '');
        if ($name === '') {
            flash('error', 'Godam name is required.');
        } else {
            try {
                db()->prepare('INSERT INTO godams (name, address) VALUES (?, ?)')->execute([$name, $address ?: null]);
                flash('success', 'Godam added.');
            } catch (Throwable $e) {
                flash('error', 'Ye godam name pehle se hai.');
            }
        }
    } elseif ($action === 'update') {
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $address = trim($_POST['address'] ?? '');
        db()->prepare('UPDATE godams SET name=?, address=? WHERE id=?')->execute([$name, $address ?: null, $id]);
        flash('success', 'Godam updated.');
    } elseif ($action === 'toggle') {
        $id = (int)($_POST['id'] ?? 0);
        db()->prepare('UPDATE godams SET is_active = 1 - is_active WHERE id = ?')->execute([$id]);
        flash('success', 'Godam status updated.');
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $hasStock = (int)db()->query('SELECT COUNT(*) FROM stock_movements WHERE godam_id = ' . $id)->fetchColumn();
        if ($hasStock) {
            flash('error', 'Is godam par movements hai - permanently delete nahi ho sakta. "Deactivate" karo.');
        } else {
            db()->prepare('DELETE FROM godams WHERE id = ?')->execute([$id]);
            flash('success', 'Godam deleted.');
        }
    }
    redirect('superadmin/godams.php');
}

$godams = db()->query("
  SELECT g.*,
    (SELECT COALESCE(SUM(qty),0) FROM product_stock ps WHERE ps.godam_id = g.id) AS total_qty,
    (SELECT COUNT(DISTINCT product_id) FROM product_stock ps WHERE ps.godam_id = g.id AND qty > 0) AS product_count
  FROM godams g ORDER BY g.is_active DESC, g.name
")->fetchAll();

require __DIR__ . '/../includes/header.php';
?>
<div class="card">
  <h3 class="mt-0">Add Godam / Warehouse</h3>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="create">
    <div class="form-row">
      <div class="form-group"><label>Godam Name *</label><input name="name" required placeholder="e.g. Main Godam"></div>
      <div class="form-group"><label>Address (optional)</label><input name="address" placeholder="e.g. Sanjay Nagar, Indore"></div>
    </div>
    <button class="btn" type="submit">Add Godam</button>
  </form>
</div>

<div class="card">
  <h3 class="mt-0">All Godams</h3>
  <?php if (!$godams): ?>
    <p class="text-muted">No godams yet. Pehla godam add karein.</p>
  <?php else: ?>
  <table>
    <tr><th>Name</th><th>Address</th><th>Products / Total Qty</th><th>Status</th><th style="min-width:220px">Actions</th></tr>
    <?php foreach ($godams as $g): $fid = 'gd-' . (int)$g['id']; ?>
    <tr>
      <td>
        <form id="<?= $fid ?>" method="post" style="margin:0">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="update">
          <input type="hidden" name="id" value="<?= (int)$g['id'] ?>">
          <input form="<?= $fid ?>" name="name" value="<?= e($g['name']) ?>" style="width:100%">
        </form>
      </td>
      <td><input form="<?= $fid ?>" name="address" value="<?= e($g['address']) ?>" placeholder="—" style="width:100%"></td>
      <td><?= (int)$g['product_count'] ?> products<br><span class="text-muted"><?= rtrim(rtrim(number_format((float)$g['total_qty'], 2), '0'), '.') ?> qty</span></td>
      <td><span class="badge <?= $g['is_active'] ? 'green' : 'gray' ?>"><?= $g['is_active'] ? 'Active' : 'Inactive' ?></span></td>
      <td>
        <div style="display:flex;gap:6px;flex-wrap:wrap">
          <button form="<?= $fid ?>" class="btn small" type="submit">Save</button>
          <form method="post" style="margin:0" onsubmit="return confirm('Change godam status?')">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="toggle">
            <input type="hidden" name="id" value="<?= (int)$g['id'] ?>">
            <button class="btn small secondary" type="submit"><?= $g['is_active'] ? 'Deactivate' : 'Activate' ?></button>
          </form>
          <form method="post" style="margin:0" onsubmit="return doubleConfirm('Delete godam <?= e(addslashes($g['name'])) ?> permanently?', 'Are you absolutely sure? This CANNOT be undone.')">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= (int)$g['id'] ?>">
            <button class="btn small danger" type="submit">Delete</button>
          </form>
        </div>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>
  <?php endif; ?>
</div>

<div class="card">
  <a class="btn" href="<?= e(base_url('superadmin/stock_entry.php')) ?>">+ Add Stock (Opening / Purchase)</a>
  <a class="btn secondary" href="<?= e(base_url('superadmin/stock_view.php')) ?>">View All Stock</a>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
