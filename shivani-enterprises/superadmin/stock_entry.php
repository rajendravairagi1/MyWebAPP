<?php
require_once __DIR__ . '/../includes/functions.php';
$u = require_role('super_admin');
$pageTitle = 'Stock Entry';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $productId = (int)($_POST['product_id'] ?? 0);
    $godamId = (int)($_POST['godam_id'] ?? 0);
    $qty = (float)($_POST['qty'] ?? 0);
    $type = $_POST['movement_type'] ?? 'purchase';
    $note = trim($_POST['note'] ?? '');

    // direct_sale is the "bina bill ke minus" flow - user enters a positive
    // qty, we store it as a negative delta with movement_type='sale'.
    if (!in_array($type, ['opening', 'purchase', 'adjustment', 'direct_sale'], true)) {
        $errors[] = 'Invalid stock entry type.';
    }
    if ($productId <= 0) $errors[] = 'Product select karein.';
    if ($godamId <= 0)   $errors[] = 'Godown select karein.';
    if ($qty == 0)       $errors[] = 'Qty zero nahi ho sakti (Adjustment ke liye -5 negative bhi de sakte ho).';
    if ($type === 'direct_sale' && $qty < 0) $errors[] = 'Direct Sale ke liye positive qty daalein (jitna maal godown se nikala).';

    if (!$errors) {
        // Direct Sale: qty entered as positive, but stock reduces - flip sign
        // and file it as a 'sale' movement with no invoice reference.
        $delta = ($type === 'direct_sale') ? -abs($qty) : $qty;
        $storedType = ($type === 'direct_sale') ? 'sale' : $type;
        $storedNote = ($type === 'direct_sale')
            ? ('Direct sale (no bill)' . ($note !== '' ? ' — ' . $note : ''))
            : ($note ?: null);

        // Never let stock cross zero from below - block the entry if the
        // resulting qty would be negative.
        $curStmt = db()->prepare('SELECT qty FROM product_stock WHERE product_id = ? AND godam_id = ?');
        $curStmt->execute([$productId, $godamId]);
        $curQ = (float)($curStmt->fetchColumn() ?: 0);
        if ($curQ + $delta < 0) {
            $errors[] = 'Godown me sirf ' . rtrim(rtrim(number_format($curQ, 2), '0'), '.') . ' bacha hai — utni hi qty nikal sakte ho, uske se zyada nahi (stock 0 se neeche nahi ja sakta).';
        }
    }

    if (!$errors) {
        $db = db();
        $db->beginTransaction();
        try {
            $db->prepare('INSERT INTO product_stock (product_id, godam_id, qty) VALUES (?,?,?) ON DUPLICATE KEY UPDATE qty = qty + VALUES(qty)')
               ->execute([$productId, $godamId, $delta]);
            $db->prepare('INSERT INTO stock_movements (product_id, godam_id, movement_type, qty_change, ref_type, admin_id, note) VALUES (?,?,?,?,?,?,?)')
               ->execute([$productId, $godamId, $storedType, $delta, $type === 'direct_sale' ? 'direct_sale' : null, $u['id'], $storedNote]);
            $db->commit();
            flash('success', $type === 'direct_sale' ? 'Godown se stock minus ho gaya.' : 'Stock updated.');
            redirect('superadmin/stock_view.php');
        } catch (Throwable $e) {
            $db->rollBack();
            $errors[] = 'DB error: ' . $e->getMessage();
        }
    }
}

$products = db()->query('SELECT id, name, unit FROM products WHERE is_active = 1 ORDER BY name')->fetchAll();
$godams = db()->query('SELECT id, name FROM godams WHERE is_active = 1 ORDER BY name')->fetchAll();

// Optional deep-link params: from the Godowns page "− Reduce" button we
// arrive with the target product+godown already picked and the direct
// sale type pre-selected.
$preProduct = (int)($_GET['product_id'] ?? 0);
$preGodam   = (int)($_GET['godam_id'] ?? 0);
$preType    = $_GET['type'] ?? 'purchase';
if (!in_array($preType, ['opening','purchase','adjustment','direct_sale'], true)) $preType = 'purchase';

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
        <select name="movement_type" id="mtype">
          <option value="purchase">Purchase (nayi khareed) — stock badhega</option>
          <option value="opening">Opening Stock (pehla balance) — stock badhega</option>
          <option value="direct_sale">Direct Sale (bina bill ke) — stock kam hoga</option>
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
      <div class="form-group"><label>Qty *</label><input type="number" step="0.01" name="qty" id="qty" required placeholder="e.g. 10"></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label>Note (optional)</label><input name="note" id="note" placeholder="e.g. Invoice #XYZ se khareeda"></div>
    </div>
    <p class="text-muted" id="type-hint" style="margin:-4px 0 12px;font-size:13px"></p>
    <button class="btn" type="submit" id="submitBtn">Save Stock Entry</button>
  </form>
  <script>
    (function () {
      var sel = document.getElementById('mtype');
      var hint = document.getElementById('type-hint');
      var qty = document.getElementById('qty');
      var note = document.getElementById('note');
      var btn = document.getElementById('submitBtn');
      var config = {
        purchase:    { hint: 'Stock badhega — jitna maal godown me aya wo positive qty daalein.', qp: 'e.g. 10',                  np: 'e.g. Invoice #XYZ se khareeda',      btn: 'Add Stock' },
        opening:     { hint: 'Pehli baar godown me kitna maal tha — positive qty daalein.',       qp: 'e.g. 20',                  np: 'e.g. Opening balance',              btn: 'Add Opening Stock' },
        direct_sale: { hint: 'Bina bill ke bechi hui qty daalein — stock godown se turant minus ho jayega.', qp: 'jitna maal beacha (e.g. 3)', np: 'e.g. Cash sale, walk-in customer', btn: 'Reduce Stock (Direct Sale)' },
        adjustment:  { hint: 'Correction — positive (+) ya negative (-) qty daal sakte ho.',      qp: 'e.g. -2 ya 5',              np: 'e.g. Damage / found extra',         btn: 'Save Adjustment' }
      };
      function apply() {
        var c = config[sel.value] || config.purchase;
        hint.textContent = c.hint;
        qty.placeholder = c.qp;
        note.placeholder = c.np;
        btn.textContent = c.btn;
      }
      sel.addEventListener('change', apply);
      apply();
    })();
  </script>
  <?php endif; ?>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
