<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../lib/invoice_pdf.php';
require_role('super_admin');
$pageTitle = 'Rate List';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $allIds = $_POST['all_ids'] ?? []; // every product id that was on the form, so an unchecked checkbox can still be detected
    $prices = $_POST['price'] ?? []; // [product_id => rate-string]
    $shown = $_POST['show'] ?? []; // [product_id => '1'] only present for checked ones
    $stmtPrice = db()->prepare('UPDATE products SET default_price = ? WHERE id = ?');
    $stmtShow = db()->prepare('UPDATE products SET show_in_rate_list = ? WHERE id = ?');
    $updated = 0;
    foreach ($allIds as $pid) {
        $pid = (int)$pid;
        if ($pid <= 0) continue;
        $raw = trim((string)($prices[$pid] ?? ''));
        if ($raw !== '' && (float)$raw >= 0) {
            $stmtPrice->execute([(float)$raw, $pid]);
        }
        $stmtShow->execute([isset($shown[$pid]) ? 1 : 0, $pid]);
        $updated++;
    }
    flash('success', $updated . ' product' . ($updated === 1 ? '' : 's') . ' ki rate list settings update ho gayi.');
    redirect('superadmin/rate_list.php');
}

// ?pdf=1 streams the PDF inline instead of rendering the HTML page below -
// same pattern used by sale_view.php for invoices.
if (($_GET['pdf'] ?? '') === '1') {
    $products = db()->query('SELECT name, unit, default_price FROM products WHERE is_active = 1 AND show_in_rate_list = 1 ORDER BY name')->fetchAll();
    render_rate_list_pdf($products, 'D');
    exit;
}

// Edit table shows every active product (so a hidden one can be turned back
// on); the preview/PDF/PNG only ever include the ones marked to show.
$products = db()->query('SELECT id, name, unit, default_price, show_in_rate_list FROM products WHERE is_active = 1 ORDER BY name')->fetchAll();
$visibleProducts = array_values(array_filter($products, fn($p) => (int)$p['show_in_rate_list'] === 1));
$pdfUrl = base_url('superadmin/rate_list.php') . '?pdf=1';

require __DIR__ . '/../includes/header.php';
?>
<div class="card">
  <h3 class="mt-0">Rate List</h3>
  <p class="text-muted" style="font-size:13px">Yahan sabhi products ki rate dikhti hai (Products page wali Selling Price se). Agar kisi ki rate badalni hai to yahin edit karke "Update Rates" dabao. Jis product ki rate list me dena hi nahi hai — jaise koi product abhi nahi dena, ya rate decide nahi hai — uska "List me dikhao" wala tick hata do; wo product Stock Entry/Sale me pehle jaisa active rahega, sirf Rate List (PDF/PNG) se hat jayega.</p>
  <?php if (!$products): ?>
    <div class="alert error">Pehle Products add karein — <a href="<?= e(base_url('superadmin/products.php')) ?>">Products page</a>.</div>
  <?php else: ?>
  <form method="post">
    <?= csrf_field() ?>
    <div class="rate-rows">
      <div class="rate-row rate-row-head">
        <div class="li-head">Product</div>
        <div class="li-head">Unit</div>
        <div class="li-head">Rate (Rs.)</div>
        <div class="li-head">List me dikhao</div>
      </div>
      <?php foreach ($products as $p): ?>
      <div class="rate-row">
        <input type="hidden" name="all_ids[]" value="<?= (int)$p['id'] ?>">
        <div class="rr-name"><?= e($p['name']) ?></div>
        <div class="rr-unit text-muted"><?= e($p['unit']) ?></div>
        <div class="li-field"><input type="number" step="0.01" min="0" name="price[<?= (int)$p['id'] ?>]" value="<?= e((string)$p['default_price']) ?>"></div>
        <div class="rr-show"><input type="checkbox" name="show[<?= (int)$p['id'] ?>]" value="1" <?= $p['show_in_rate_list'] ? 'checked' : '' ?>></div>
      </div>
      <?php endforeach; ?>
    </div>
    <button class="btn" type="submit" style="margin-top:14px">Update Rates</button>
  </form>
  <?php endif; ?>
</div>

<?php if ($products): ?>
<div class="card">
  <h3 class="mt-0">Preview — ye hi PDF / PNG me jayega</h3>
  <?php if (!$visibleProducts): ?>
    <div class="alert error">Kisi bhi product ka "List me dikhao" tick nahi hai, isliye rate list khali hai — upar se kam se kam ek product tick karein.</div>
  <?php else: ?>
  <div class="rate-list-actions">
    <a class="btn small" href="<?= e($pdfUrl) ?>" target="_blank">&#11015; Download PDF</a>
    <button type="button" class="btn small secondary" id="downloadPngBtn">&#11015; Download PNG</button>
  </div>
  <div class="rate-list-preview" id="rateListPreview">
    <div class="rlp-header">
      <div class="rlp-company"><?= e(get_setting('company_name', APP_NAME)) ?></div>
      <?php $companyLine = trim(get_setting('company_address', '') . '  ' . get_setting('company_phone', '')); ?>
      <?php if ($companyLine !== ''): ?><div class="rlp-sub"><?= e($companyLine) ?></div><?php endif; ?>
    </div>
    <div class="rlp-title-row">
      <span class="rlp-title">RATE LIST</span>
      <span class="rlp-date">Date: <?= e(date('d-M-Y')) ?></span>
    </div>
    <table class="rlp-table">
      <thead>
        <tr><th style="width:36px">#</th><th>Product</th><th style="width:100px">Unit</th><th style="width:120px;text-align:right">Rate (Rs.)</th></tr>
      </thead>
      <tbody>
        <?php foreach ($visibleProducts as $i => $p): ?>
        <tr>
          <td><?= $i + 1 ?></td>
          <td><?= e($p['name']) ?></td>
          <td><?= e($p['unit']) ?></td>
          <td style="text-align:right"><?= e(number_format((float)$p['default_price'], 2)) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <div class="rlp-footer">This is a computer generated rate list</div>
  </div>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js" integrity="sha512-BNaRQnYJYiPSqHHDb58B0yaPfCu+Wgds8Gp/gU33kqBtgNS4tSPHuGibyoeqMV/TJlSKda6FXzoEyYGjTe+vXA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script>
  document.getElementById('downloadPngBtn').addEventListener('click', function () {
    var btn = this;
    btn.disabled = true;
    btn.textContent = 'Generating…';
    html2canvas(document.getElementById('rateListPreview'), { scale: 2, backgroundColor: '#ffffff' }).then(function (canvas) {
      var link = document.createElement('a');
      link.download = 'rate-list-<?= e(date('Y-m-d')) ?>.png';
      link.href = canvas.toDataURL('image/png');
      link.click();
      btn.disabled = false;
      btn.textContent = '⬇ Download PNG';
    }).catch(function () {
      alert('PNG banane me error aa gaya, dobara try karein.');
      btn.disabled = false;
      btn.textContent = '⬇ Download PNG';
    });
  });
  </script>
  <?php endif; ?>
</div>
<?php endif; ?>

<?php require __DIR__ . '/../includes/footer.php'; ?>
