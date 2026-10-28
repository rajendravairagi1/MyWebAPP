<?php
require_once __DIR__ . '/../includes/functions.php';
$u = require_role('super_admin');
$pageTitle = 'Stock Entry';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $godamId = (int)($_POST['godam_id'] ?? 0);
    $type = $_POST['movement_type'] ?? 'purchase';
    $batchNote = trim($_POST['note'] ?? '');
    $qtys = $_POST['qty'] ?? []; // [product_id => qty-string]

    // direct_sale is the "bina bill ke minus" flow - user enters a positive
    // qty, we store it as a negative delta with movement_type='sale'.
    if (!in_array($type, ['opening', 'purchase', 'adjustment', 'direct_sale'], true)) {
        $errors[] = 'Invalid stock entry type.';
    }
    if ($godamId <= 0) $errors[] = 'Godown select karein.';

    // Every product on the page is a row; only the ones the user actually
    // typed a qty into become lines - blank rows are simply skipped, no
    // need to pick products one by one from a dropdown.
    $lines = [];
    foreach ($qtys as $pid => $raw) {
        $pid = (int)$pid;
        $raw = trim((string)$raw);
        if ($pid <= 0 || $raw === '') continue;
        $qty = (float)$raw;
        if ($qty == 0) { $errors[] = 'Qty zero nahi ho sakti (jis product ki entry nahi karni usey khali chhod dein).'; continue; }
        if ($type === 'direct_sale' && $qty < 0) { $errors[] = 'Direct Sale ke liye positive qty daalein.'; continue; }
        $lines[] = ['product_id' => $pid, 'qty' => $qty];
    }
    if (!$lines && !$errors) $errors[] = 'Kam se kam ek product me qty bharein.';

    // Never let stock cross zero from below - check every line's resulting
    // qty before writing anything.
    if (!$errors) {
        foreach ($lines as $ln) {
            $delta = ($type === 'direct_sale') ? -abs($ln['qty']) : $ln['qty'];
            $curStmt = db()->prepare('SELECT qty FROM product_stock WHERE product_id = ? AND godam_id = ?');
            $curStmt->execute([$ln['product_id'], $godamId]);
            $curQ = (float)($curStmt->fetchColumn() ?: 0);
            if ($curQ + $delta < 0) {
                $pn = db()->prepare('SELECT name FROM products WHERE id = ?');
                $pn->execute([$ln['product_id']]);
                $errors[] = ($pn->fetchColumn() ?: 'Product') . ' — godown me sirf ' . rtrim(rtrim(number_format($curQ, 2), '0'), '.') . ' bacha hai, utni hi nikal sakte ho.';
            }
        }
    }

    if (!$errors) {
        $db = db();
        $db->beginTransaction();
        try {
            $stockUpsert = $db->prepare('INSERT INTO product_stock (product_id, godam_id, qty) VALUES (?,?,?) ON DUPLICATE KEY UPDATE qty = qty + VALUES(qty)');
            $movStmt = $db->prepare('INSERT INTO stock_movements (product_id, godam_id, movement_type, qty_change, ref_type, admin_id, note) VALUES (?,?,?,?,?,?,?)');
            foreach ($lines as $ln) {
                $delta = ($type === 'direct_sale') ? -abs($ln['qty']) : $ln['qty'];
                $storedType = ($type === 'direct_sale') ? 'sale' : $type;
                $storedNote = ($type === 'direct_sale')
                    ? ('Direct sale (no bill)' . ($batchNote !== '' ? ' — ' . $batchNote : ''))
                    : ($batchNote ?: null);
                $stockUpsert->execute([$ln['product_id'], $godamId, $delta]);
                $movStmt->execute([$ln['product_id'], $godamId, $storedType, $delta, $type === 'direct_sale' ? 'direct_sale' : null, $u['id'], $storedNote]);
            }
            $db->commit();
            $n = count($lines);
            flash('success', $type === 'direct_sale'
                ? ($n . ' product' . ($n > 1 ? 's' : '') . ' ka stock godown se minus ho gaya.')
                : ($n . ' product' . ($n > 1 ? 's' : '') . ' ki stock entry save ho gayi.'));
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
// arrive with the target godown already picked and the direct sale type
// pre-selected.
$preGodam = (int)($_GET['godam_id'] ?? 0);
$preType  = $_GET['type'] ?? 'purchase';
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
  <form method="post" id="stockForm">
    <?= csrf_field() ?>
    <div class="form-row">
      <div class="form-group">
        <label>Type</label>
        <select name="movement_type" id="mtype">
          <option value="purchase" <?= $preType === 'purchase' ? 'selected' : '' ?>>Purchase (nayi khareed) — stock badhega</option>
          <option value="opening" <?= $preType === 'opening' ? 'selected' : '' ?>>Opening Stock (pehla balance) — stock badhega</option>
          <option value="direct_sale" <?= $preType === 'direct_sale' ? 'selected' : '' ?>>Direct Sale (bina bill ke) — stock kam hoga</option>
          <option value="adjustment" <?= $preType === 'adjustment' ? 'selected' : '' ?>>Adjustment (correction, +/-)</option>
        </select>
      </div>
      <div class="form-group">
        <label>Godown *</label>
        <select name="godam_id" required>
          <?php foreach ($godams as $g): ?><option value="<?= (int)$g['id'] ?>" <?= $preGodam === (int)$g['id'] ? 'selected' : '' ?>><?= e($g['name']) ?></option><?php endforeach; ?>
        </select>
      </div>
    </div>
    <div class="form-row">
      <div class="form-group"><label>Note (optional, pure batch ke liye)</label><input name="note" id="note" placeholder="e.g. Invoice #XYZ se khareeda"></div>
    </div>
    <p class="text-muted" id="type-hint" style="margin:-4px 0 14px;font-size:13px"></p>

    <p class="text-muted" style="font-size:13px;margin-bottom:8px">Har product ki line pehle se bani hui hai — jis product ki stock aayi/gayi hai sirf uski Qty bhar dein, baaki khali chhod dein.</p>
    <div class="stock-items" id="lineItems">
      <div class="stock-line-row stock-line-head">
        <div class="li-head">Product</div>
        <div class="li-head">Qty</div>
      </div>
      <?php foreach ($products as $p): ?>
      <div class="stock-line-row">
        <div class="stock-line-name"><?= e($p['name']) ?> <span class="text-muted">(<?= e($p['unit']) ?>)</span></div>
        <div class="li-field"><input type="number" step="0.01" name="qty[<?= (int)$p['id'] ?>]" class="qty-input" placeholder="—"></div>
      </div>
      <?php endforeach; ?>
    </div>

    <button class="btn" type="submit" id="submitBtn" style="margin-top:16px">Save Stock Entry</button>
  </form>
  <script>
    (function () {
      var sel = document.getElementById('mtype');
      var hint = document.getElementById('type-hint');
      var note = document.getElementById('note');
      var btn = document.getElementById('submitBtn');
      var config = {
        purchase:    { hint: 'Stock badhega — jitna maal godown me aya wo positive qty daalein.', np: 'e.g. Invoice #XYZ se khareeda',      btn: 'Add Stock' },
        opening:     { hint: 'Pehli baar godown me kitna maal tha — positive qty daalein.',       np: 'e.g. Opening balance',              btn: 'Add Opening Stock' },
        direct_sale: { hint: 'Bina bill ke bechi hui qty daalein — stock godown se turant minus ho jayega.', np: 'e.g. Cash sale, walk-in customer', btn: 'Reduce Stock (Direct Sale)' },
        adjustment:  { hint: 'Correction — positive (+) ya negative (-) qty daal sakte ho.',      np: 'e.g. Damage / found extra',         btn: 'Save Adjustment' }
      };
      function apply() {
        var c = config[sel.value] || config.purchase;
        hint.textContent = c.hint;
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
