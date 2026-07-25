<?php
require_once __DIR__ . '/includes/functions.php';
$u = require_login();

$saleId = (int)($_GET['id'] ?? 0);
$sale = null;
$existingItems = [];
if ($saleId) {
    $stmt = db()->prepare('SELECT * FROM sales WHERE id = ?');
    $stmt->execute([$saleId]);
    $sale = $stmt->fetch();
    if (!$sale) { http_response_code(404); die('Sale not found.'); }
    $customerId = (int)$sale['customer_id'];
    $itemsStmt = db()->prepare('SELECT * FROM sale_items WHERE sale_id = ?');
    $itemsStmt->execute([$saleId]);
    $existingItems = $itemsStmt->fetchAll();
} else {
    $customerId = (int)($_GET['customer_id'] ?? $_POST['customer_id'] ?? 0);
}

$stmt = db()->prepare('SELECT * FROM customers WHERE id = ?');
$stmt->execute([$customerId]);
$customer = $stmt->fetch();
if (!$customer) { http_response_code(404); die('Customer not found.'); }
if ($u['role'] !== 'super_admin' && (int)$customer['admin_id'] !== (int)$u['id']) {
    http_response_code(403); die('Access denied.');
}
$pageTitle = $sale ? 'Edit Invoice ' . $sale['invoice_no'] : 'New Sale / Invoice';

$products = db()->query('SELECT * FROM products WHERE is_active = 1 ORDER BY name')->fetchAll();
$errors = [];

if ($sale && $_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    verify_csrf();
    db()->prepare('DELETE FROM sales WHERE id = ?')->execute([$saleId]); // sale_items cascades; payments.sale_id -> NULL
    flash('success', 'Invoice deleted.');
    redirect('customer_view.php?id=' . $customerId);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? 'save') === 'save') {
    verify_csrf();
    $saleDate = $_POST['sale_date'] ?? date('Y-m-d');
    $notes = trim($_POST['notes'] ?? '');
    $productIds = $_POST['product_id'] ?? [];
    $customNames = $_POST['custom_name'] ?? [];
    $qtys = $_POST['qty'] ?? [];
    $prices = $_POST['price'] ?? [];

    $items = [];
    $total = 0;
    foreach ($productIds as $i => $pid) {
        $pid = (int)$pid;
        $qty = (float)($qtys[$i] ?? 0);
        $price = (float)($prices[$i] ?? 0);
        $customName = trim($customNames[$i] ?? '');
        if ($qty <= 0) continue;
        if ($pid <= 0) {
            // "Other / Custom Product" line - not tied to the products catalog.
            if ($customName === '') continue;
            $lineTotal = round($qty * $price, 2);
            $items[] = ['product_id' => null, 'product_name' => $customName, 'qty' => $qty, 'price' => $price, 'line_total' => $lineTotal];
        } else {
            $lineTotal = round($qty * $price, 2);
            $items[] = ['product_id' => $pid, 'product_name' => null, 'qty' => $qty, 'price' => $price, 'line_total' => $lineTotal];
        }
        $total += $lineTotal;
    }
    if (!$items) $errors[] = 'Add at least one product line with quantity and price (or use "Other" for a custom item).';

    if (!$errors) {
        $db = db();
        $db->beginTransaction();
        try {
            $prodNameStmt = $db->prepare('SELECT name FROM products WHERE id = ?');
            $itemStmt = $db->prepare('INSERT INTO sale_items (sale_id, product_id, product_name, qty, price, line_total) VALUES (?,?,?,?,?,?)');

            if ($sale) {
                $stmt = $db->prepare('UPDATE sales SET sale_date=?, total_amount=?, notes=? WHERE id=?');
                $stmt->execute([$saleDate, $total, $notes ?: null, $saleId]);
                $db->prepare('DELETE FROM sale_items WHERE sale_id = ?')->execute([$saleId]);
                $newSaleId = $saleId;
                $invoiceNo = $sale['invoice_no'];
            } else {
                $invoiceNo = next_invoice_no();
                $stmt = $db->prepare('INSERT INTO sales (invoice_no, customer_id, admin_id, sale_date, total_amount, notes, created_by) VALUES (?,?,?,?,?,?,?)');
                $stmt->execute([$invoiceNo, $customerId, $customer['admin_id'], $saleDate, $total, $notes ?: null, $u['id']]);
                $newSaleId = (int)$db->lastInsertId();
            }

            foreach ($items as $it) {
                if ($it['product_id'] === null) {
                    $pname = $it['product_name'];
                } else {
                    $prodNameStmt->execute([$it['product_id']]);
                    $pname = $prodNameStmt->fetchColumn() ?: 'Product';
                }
                $itemStmt->execute([$newSaleId, $it['product_id'], $pname, $it['qty'], $it['price'], $it['line_total']]);
            }
            $db->commit();
            flash('success', $sale ? 'Invoice ' . $invoiceNo . ' updated.' : 'Sale recorded. Invoice ' . $invoiceNo . ' generated.');
            redirect('sale_view.php?id=' . $newSaleId);
        } catch (Throwable $e) {
            $db->rollBack();
            $errors[] = 'Could not save sale: ' . $e->getMessage();
        }
    }
}

require __DIR__ . '/includes/header.php';
?>
<p><a href="<?= e(base_url($sale ? 'sale_view.php?id=' . $saleId : 'customer_view.php?id=' . $customerId)) ?>">&larr; Back</a></p>
<div class="card">
  <h3 class="mt-0"><?= $sale ? 'Edit Invoice ' . e($sale['invoice_no']) : 'New Sale' ?> for <?= e($customer['name']) ?></h3>
  <?php foreach ($errors as $err): ?><div class="alert error"><?= e($err) ?></div><?php endforeach; ?>
  <form method="post" id="saleForm">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="customer_id" value="<?= (int)$customerId ?>">
    <div class="form-row">
      <div class="form-group"><label>Sale Date</label><input type="date" name="sale_date" value="<?= e($sale['sale_date'] ?? date('Y-m-d')) ?>" required></div>
      <div class="form-group"><label>Notes</label><input name="notes" placeholder="Optional" value="<?= e($sale['notes'] ?? '') ?>"></div>
    </div>

    <div class="line-items" id="lineItems">
      <div class="line-item-row">
        <div class="li-head">Product</div>
        <div class="li-head">Qty</div>
        <div class="li-head">Price (Rs.)</div>
        <div class="li-head">Amount</div>
        <div class="li-head"></div>
      </div>
      <?php $rows = $existingItems ?: [null]; foreach ($rows as $row): ?>
      <div class="line-item-row line-row">
        <div class="li-field li-product">
          <select name="product_id[]" class="prod-select" required>
            <option value="">-- select product --</option>
            <?php foreach ($products as $p): ?>
              <option value="<?= (int)$p['id'] ?>" data-price="<?= e($p['default_price']) ?>" <?= ($row && (int)$row['product_id'] === (int)$p['id']) ? 'selected' : '' ?>><?= e($p['name']) ?> (<?= e($p['unit']) ?>)</option>
            <?php endforeach; ?>
            <option value="0" <?= ($row && $row['product_id'] === null) ? 'selected' : '' ?>>&#10022; Other / Custom Product</option>
          </select>
          <input type="text" name="custom_name[]" class="custom-name" placeholder="Enter product name"
            style="<?= ($row && $row['product_id'] === null) ? 'display:block' : 'display:none' ?>;margin-top:8px"
            value="<?= ($row && $row['product_id'] === null) ? e($row['product_name']) : '' ?>">
        </div>
        <div class="li-field"><input type="number" step="0.01" min="0.01" name="qty[]" class="qty" value="<?= e($row['qty'] ?? '1') ?>" placeholder="Qty" required></div>
        <div class="li-field"><input type="number" step="0.01" name="price[]" class="price" placeholder="Price (Rs.)" required value="<?= e($row['price'] ?? '') ?>"></div>
        <div class="li-field li-total-field"><span class="li-mobile-label">Amount: </span><span class="line-total"><?= e(number_format((float)($row['line_total'] ?? 0), 2)) ?></span></div>
        <div class="li-field li-remove-field"><button type="button" class="li-remove remove-row" title="Remove line">&times;</button></div>
      </div>
      <?php endforeach; ?>
    </div>
    <p><button type="button" class="btn small secondary" id="addRow">+ Add Product Line</button></p>
    <p style="font-size:18px"><strong>Total: Rs. <span id="grandTotal"><?= e(number_format((float)($sale['total_amount'] ?? 0), 2)) ?></span></strong></p>
    <p class="text-muted">Note: price per line editable hai — bargaining ya alag-alag deal ke hisaab se adjust kar sakte ho. Agar koi product list me nahi hai to "Other / Custom Product" chun kar naam type kar do.</p>
    <button class="btn" type="submit"><?= $sale ? 'Save Changes' : 'Save Sale & Generate Invoice' ?></button>
  </form>
  <?php if ($sale): ?>
    <form method="post" style="margin-top:10px" onsubmit="return doubleConfirm('Delete invoice <?= e(addslashes($sale['invoice_no'])) ?> permanently?', 'Are you absolutely sure? This is PERMANENT and cannot be undone.')">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="delete">
      <button class="btn small danger" type="submit">Delete Invoice</button>
    </form>
  <?php endif; ?>
</div>

<script>
function recalc() {
  let grand = 0;
  document.querySelectorAll('.line-row').forEach(row => {
    const qty = parseFloat(row.querySelector('.qty').value) || 0;
    const price = parseFloat(row.querySelector('.price').value) || 0;
    const lt = qty * price;
    row.querySelector('.line-total').textContent = lt.toFixed(2);
    grand += lt;
  });
  document.getElementById('grandTotal').textContent = grand.toFixed(2);
}
function wireRow(row) {
  const select = row.querySelector('.prod-select');
  const customInput = row.querySelector('.custom-name');
  select.addEventListener('change', e => {
    const opt = e.target.selectedOptions[0];
    if (select.value === '0') {
      customInput.style.display = 'block';
      customInput.required = true;
      row.querySelector('.price').value = '';
    } else {
      customInput.style.display = 'none';
      customInput.required = false;
      customInput.value = '';
      if (opt && opt.dataset.price) row.querySelector('.price').value = opt.dataset.price;
    }
    recalc();
  });
  row.querySelectorAll('.qty,.price').forEach(inp => inp.addEventListener('input', recalc));
  row.querySelector('.remove-row').addEventListener('click', () => {
    if (document.querySelectorAll('.line-row').length > 1) { row.remove(); recalc(); }
  });
}
document.querySelectorAll('.line-row').forEach(wireRow);
document.getElementById('addRow').addEventListener('click', () => {
  const container = document.getElementById('lineItems');
  const first = document.querySelector('.line-row');
  const clone = first.cloneNode(true);
  clone.querySelectorAll('input').forEach(i => {
    if (i.classList.contains('qty')) i.value = 1;
    else i.value = '';
  });
  clone.querySelector('.custom-name').style.display = 'none';
  clone.querySelector('.custom-name').required = false;
  clone.querySelector('.prod-select').value = '';
  clone.querySelector('.line-total').textContent = '0.00';
  container.appendChild(clone);
  wireRow(clone);
});
recalc();
</script>
<?php require __DIR__ . '/includes/footer.php'; ?>
