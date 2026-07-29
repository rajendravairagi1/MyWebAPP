<?php
require_once __DIR__ . '/../includes/functions.php';
$u = require_role('super_admin');
$pageTitle = 'Godowns / Warehouses';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';
    if ($action === 'create') {
        $name = trim($_POST['name'] ?? '');
        $address = trim($_POST['address'] ?? '');
        if ($name === '') {
            flash('error', 'Godown name is required.');
        } else {
            try {
                db()->prepare('INSERT INTO godams (name, address) VALUES (?, ?)')->execute([$name, $address ?: null]);
                flash('success', 'Godown added.');
            } catch (Throwable $e) {
                flash('error', 'Ye Godown name pehle se hai.');
            }
        }
    } elseif ($action === 'update') {
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $address = trim($_POST['address'] ?? '');
        db()->prepare('UPDATE godams SET name=?, address=? WHERE id=?')->execute([$name, $address ?: null, $id]);
        flash('success', 'Godown updated.');
    } elseif ($action === 'toggle') {
        $id = (int)($_POST['id'] ?? 0);
        db()->prepare('UPDATE godams SET is_active = 1 - is_active WHERE id = ?')->execute([$id]);
        flash('success', 'Godown status updated.');
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $hasStock = (int)db()->query('SELECT COUNT(*) FROM stock_movements WHERE godam_id = ' . $id)->fetchColumn();
        if ($hasStock) {
            flash('error', 'Is Godown par movements hai - permanently delete nahi ho sakta. "Deactivate" karo.');
        } else {
            db()->prepare('DELETE FROM godams WHERE id = ?')->execute([$id]);
            flash('success', 'Godown deleted.');
        }
    } elseif ($action === 'stock_update') {
        // Manual edit of a product's qty in a specific godown - jump the
        // stock straight to the new number and record an adjustment
        // movement for the delta so history stays intact.
        $pid  = (int)($_POST['product_id'] ?? 0);
        $gid  = (int)($_POST['godam_id'] ?? 0);
        $newQ = (float)($_POST['qty'] ?? 0);
        if ($pid > 0 && $gid > 0) {
            $db = db();
            $db->beginTransaction();
            try {
                $curStmt = $db->prepare('SELECT qty FROM product_stock WHERE product_id = ? AND godam_id = ?');
                $curStmt->execute([$pid, $gid]);
                $cur = (float)($curStmt->fetchColumn() ?: 0);
                $delta = $newQ - $cur;
                $db->prepare('INSERT INTO product_stock (product_id, godam_id, qty) VALUES (?,?,?) ON DUPLICATE KEY UPDATE qty = VALUES(qty)')
                   ->execute([$pid, $gid, $newQ]);
                if (abs($delta) > 0.0001) {
                    $db->prepare('INSERT INTO stock_movements (product_id, godam_id, movement_type, qty_change, ref_type, admin_id, note) VALUES (?,?,?,?,?,?,?)')
                       ->execute([$pid, $gid, 'adjustment', $delta, 'manual_edit', $u['id'], 'Manual edit: ' . rtrim(rtrim(number_format($cur,2),'0'),'.') . ' -> ' . rtrim(rtrim(number_format($newQ,2),'0'),'.')]);
                }
                $db->commit();
                flash('success', 'Stock qty update ho gayi.');
            } catch (Throwable $e) {
                $db->rollBack();
                flash('error', 'Update fail: ' . $e->getMessage());
            }
        }
    } elseif ($action === 'stock_delete') {
        // Remove the product_stock row entirely (product no longer stored
        // in this godown). Log the removal as an adjustment for history.
        $pid = (int)($_POST['product_id'] ?? 0);
        $gid = (int)($_POST['godam_id'] ?? 0);
        if ($pid > 0 && $gid > 0) {
            $db = db();
            $db->beginTransaction();
            try {
                $curStmt = $db->prepare('SELECT qty FROM product_stock WHERE product_id = ? AND godam_id = ?');
                $curStmt->execute([$pid, $gid]);
                $cur = (float)($curStmt->fetchColumn() ?: 0);
                $db->prepare('DELETE FROM product_stock WHERE product_id = ? AND godam_id = ?')->execute([$pid, $gid]);
                if (abs($cur) > 0.0001) {
                    $db->prepare('INSERT INTO stock_movements (product_id, godam_id, movement_type, qty_change, ref_type, admin_id, note) VALUES (?,?,?,?,?,?,?)')
                       ->execute([$pid, $gid, 'adjustment', -$cur, 'manual_delete', $u['id'], 'Manual delete: product hataya, ' . rtrim(rtrim(number_format($cur,2),'0'),'.') . ' qty nikali']);
                }
                $db->commit();
                flash('success', 'Product is Godown se hata diya.');
            } catch (Throwable $e) {
                $db->rollBack();
                flash('error', 'Delete fail: ' . $e->getMessage());
            }
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

// Products in each godam for the expandable "kaun kaun sa maal rakha hai" panel.
$stockRows = db()->query("
  SELECT ps.godam_id, ps.product_id, ps.qty, p.name AS product_name, p.unit
  FROM product_stock ps
  JOIN products p ON p.id = ps.product_id
  ORDER BY p.name
")->fetchAll();
$stockByGodam = [];
foreach ($stockRows as $r) { $stockByGodam[(int)$r['godam_id']][] = $r; }

require __DIR__ . '/../includes/header.php';
?>
<div class="card">
  <h3 class="mt-0">Add Godown / Warehouse</h3>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="create">
    <div class="form-row">
      <div class="form-group"><label>Godown Name *</label><input name="name" required placeholder="e.g. Main Godown"></div>
      <div class="form-group"><label>Address (optional)</label><input name="address" placeholder="e.g. Sanjay Nagar, Indore"></div>
    </div>
    <button class="btn" type="submit">Add Godown</button>
  </form>
</div>

<h3 style="margin:8px 4px 12px">All Godowns</h3>
<?php if (!$godams): ?>
  <div class="card"><p class="text-muted mt-0">No Godowns yet. Pehla Godown add karein.</p></div>
<?php else: foreach ($godams as $g): $fid = 'gd-' . (int)$g['id']; $stk = $stockByGodam[(int)$g['id']] ?? []; ?>
  <details class="card godam-card" <?= $g['is_active'] ? 'open' : '' ?> style="padding:0;overflow:hidden">
    <summary style="cursor:pointer;list-style:none;padding:16px 18px;display:flex;align-items:center;gap:12px;flex-wrap:wrap">
      <span class="chev" style="display:inline-block;transition:transform .15s;font-size:14px;color:#94a3b8">&#9656;</span>
      <span style="font-size:17px;font-weight:600;flex:1 1 200px;min-width:0">
        <?= e($g['name']) ?>
        <?php if ($g['address']): ?><span class="text-muted" style="font-weight:400;font-size:13px">&nbsp;&middot; <?= e($g['address']) ?></span><?php endif; ?>
      </span>
      <span class="badge <?= $g['is_active'] ? 'green' : 'gray' ?>"><?= $g['is_active'] ? 'Active' : 'Inactive' ?></span>
      <span style="font-size:14px"><strong><?= (int)$g['product_count'] ?></strong> <span class="text-muted">products &middot;</span> <strong><?= rtrim(rtrim(number_format((float)$g['total_qty'], 2), '0'), '.') ?></strong> <span class="text-muted">qty</span></span>
    </summary>

    <div style="padding:0 18px 18px;border-top:1px solid rgba(148,163,184,.18)">

      <h4 style="margin:18px 0 8px;font-size:14px;text-transform:uppercase;letter-spacing:.4px;color:#64748b">Is Godown me rakha maal</h4>
      <?php if (!$stk): ?>
        <p class="text-muted" style="margin:6px 0 14px">Abhi tak is Godown me koi stock nahi rakha. <a href="<?= e(base_url('superadmin/stock_entry.php')) ?>">+ Add Stock Entry</a></p>
      <?php else: ?>
        <div class="stock-rows">
          <?php foreach ($stk as $s): $q = (float)$s['qty']; $low = $q > 0 && $q < 5; $sfid = 'sf-' . (int)$s['godam_id'] . '-' . (int)$s['product_id']; ?>
            <div class="stock-row">
              <div class="sr-name">
                <div class="sr-title"><?= e($s['product_name']) ?></div>
                <div class="sr-sub">
                  <span class="text-muted">(<?= e($s['unit']) ?>)</span>
                  <?php if ($q <= 0): ?><span class="badge gray">Out of stock</span>
                  <?php elseif ($low): ?><span class="badge orange">Low</span><?php endif; ?>
                </div>
              </div>
              <form id="<?= $sfid ?>" method="post" class="sr-qty">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="stock_update">
                <input type="hidden" name="godam_id" value="<?= (int)$s['godam_id'] ?>">
                <input type="hidden" name="product_id" value="<?= (int)$s['product_id'] ?>">
                <input type="number" step="0.01" name="qty" value="<?= rtrim(rtrim(number_format($q, 2, '.', ''), '0'), '.') ?>" required>
              </form>
              <div class="sr-actions">
                <button form="<?= $sfid ?>" class="btn small" type="submit">Save</button>
                <form method="post" style="margin:0" onsubmit="return confirm('Is product ka stock is Godown se poora hata dein? (movements history rahegi)')">
                  <?= csrf_field() ?>
                  <input type="hidden" name="action" value="stock_delete">
                  <input type="hidden" name="godam_id" value="<?= (int)$s['godam_id'] ?>">
                  <input type="hidden" name="product_id" value="<?= (int)$s['product_id'] ?>">
                  <button class="btn small danger" type="submit">Delete</button>
                </form>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <h4 style="margin:18px 0 8px;font-size:14px;text-transform:uppercase;letter-spacing:.4px;color:#64748b">Edit Godown</h4>
      <form id="<?= $fid ?>" method="post" style="margin:0">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="id" value="<?= (int)$g['id'] ?>">
        <div class="form-row">
          <div class="form-group"><label>Godown Name</label><input name="name" value="<?= e($g['name']) ?>" required></div>
          <div class="form-group"><label>Address</label><input name="address" value="<?= e($g['address']) ?>" placeholder="—"></div>
        </div>
      </form>
      <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:8px">
        <button form="<?= $fid ?>" class="btn small" type="submit">Save Changes</button>
        <form method="post" style="margin:0" onsubmit="return confirm('Change Godown status?')">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="toggle">
          <input type="hidden" name="id" value="<?= (int)$g['id'] ?>">
          <button class="btn small secondary" type="submit"><?= $g['is_active'] ? 'Deactivate' : 'Activate' ?></button>
        </form>
        <form method="post" style="margin:0" onsubmit="return doubleConfirm('Delete Godown <?= e(addslashes($g['name'])) ?> permanently?', 'Are you absolutely sure? This CANNOT be undone.')">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="id" value="<?= (int)$g['id'] ?>">
          <button class="btn small danger" type="submit">Delete</button>
        </form>
      </div>
    </div>
  </details>
<?php endforeach; endif; ?>
<style>
  .godam-card > summary::-webkit-details-marker { display:none; }
  .godam-card[open] > summary .chev { transform: rotate(90deg); }
  .godam-card > summary:hover { background: rgba(148,163,184,.06); }
</style>

<div class="card">
  <a class="btn" href="<?= e(base_url('superadmin/stock_entry.php')) ?>">+ Add Stock (Opening / Purchase)</a>
  <a class="btn secondary" href="<?= e(base_url('superadmin/stock_view.php')) ?>">View All Stock</a>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
