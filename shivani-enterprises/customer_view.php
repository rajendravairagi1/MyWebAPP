<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/lib/invoice_pdf.php';
$u = require_login();

$id = resolve_id('customer');
$stmt = db()->prepare('SELECT c.*, ad.name AS admin_name FROM customers c JOIN users ad ON ad.id = c.admin_id WHERE c.id = ?');
$stmt->execute([$id]);
$customer = $stmt->fetch();
if (!$customer) { http_response_code(404); die('Customer not found.'); }
if ($u['role'] !== 'super_admin' && (int)$customer['admin_id'] !== (int)$u['id']) {
    http_response_code(403); die('Access denied.');
}
$backBase = $u['role'] === 'super_admin' ? 'superadmin/' : 'admin/';
$pageTitle = 'Customer: ' . $customer['name'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'toggle_status') {
    verify_csrf();
    $stmt = db()->prepare("UPDATE customers SET status = IF(status = 'active', 'inactive', 'active') WHERE id = ?");
    $stmt->execute([$id]);
    flash('success', 'Customer status updated.');
    redirect('customer_view.php?c=' . sign_id('customer', $id));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    verify_csrf();
    $saleCountStmt = db()->prepare('SELECT COUNT(*) FROM sales WHERE customer_id = ?');
    $saleCountStmt->execute([$id]);
    $saleCount = (int)$saleCountStmt->fetchColumn();
    $paymentCountStmt = db()->prepare('SELECT COUNT(*) FROM payments WHERE customer_id = ?');
    $paymentCountStmt->execute([$id]);
    $paymentCount = (int)$paymentCountStmt->fetchColumn();

    if ($saleCount > 0 || $paymentCount > 0) {
        flash('error', 'Is customer ke sales/payments records hai, isliye delete nahi ho sakta (records surakshit rakhne ke liye). Iske bajaye "Deactivate" use karein.');
        redirect('customer_view.php?c=' . sign_id('customer', $id));
    }

    db()->prepare('DELETE FROM customers WHERE id = ?')->execute([$id]);
    flash('success', 'Customer deleted.');
    redirect($backBase . 'customers.php');
}

// Document upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'upload_document') {
    verify_csrf();
    try {
        $doc = handle_document_upload('document');
        if ($doc) {
            $stmt = db()->prepare('INSERT INTO customer_documents (customer_id, file_path, original_name, uploaded_by) VALUES (?,?,?,?)');
            $stmt->execute([$id, $doc['path'], $doc['original_name'], $u['id']]);
            flash('success', 'Document uploaded.');
        }
    } catch (RuntimeException $e) {
        flash('error', $e->getMessage());
    }
    redirect('customer_view.php?c=' . sign_id('customer', $id));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete_document') {
    verify_csrf();
    $docId = (int)($_POST['document_id'] ?? 0);
    $docStmt = db()->prepare('SELECT * FROM customer_documents WHERE id = ? AND customer_id = ?');
    $docStmt->execute([$docId, $id]);
    $doc = $docStmt->fetch();
    if ($doc) {
        $filePath = __DIR__ . '/' . $doc['file_path'];
        if (is_file($filePath)) { @unlink($filePath); }
        db()->prepare('DELETE FROM customer_documents WHERE id = ?')->execute([$docId]);
        flash('success', 'Document deleted.');
    }
    redirect('customer_view.php?c=' . sign_id('customer', $id));
}

if (($_GET['statement'] ?? '') === 'pdf') {
    $sales = db()->prepare('SELECT * FROM sales WHERE customer_id = ? ORDER BY sale_date');
    $sales->execute([$id]);
    $payments = db()->prepare('SELECT * FROM payments WHERE customer_id = ? ORDER BY payment_date');
    $payments->execute([$id]);
    render_statement_pdf($customer, $sales->fetchAll(), $payments->fetchAll());
    exit;
}

// Invoice-wise "paid" (only payments explicitly tagged against that
// invoice count - a general/untagged payment can't be attributed to one
// specific invoice) - once an invoice's tagged payments cover its total,
// it moves from Pending into History.
$sales = db()->prepare(
    "SELECT s.*, COALESCE((SELECT SUM(p.amount) FROM payments p WHERE p.sale_id = s.id), 0) AS paid_for_sale
     FROM sales s WHERE s.customer_id = ? ORDER BY s.sale_date DESC, s.id DESC"
);
$sales->execute([$id]);
$sales = $sales->fetchAll();
$pendingSales = [];
$historySales = [];
foreach ($sales as $s) {
    if ((float)$s['paid_for_sale'] >= (float)$s['total_amount'] - 0.01) {
        $historySales[] = $s;
    } else {
        $pendingSales[] = $s;
    }
}

$payments = db()->prepare('SELECT * FROM payments WHERE customer_id = ? ORDER BY payment_date DESC, id DESC');
$payments->execute([$id]);
$payments = $payments->fetchAll();

$totalGiven = array_sum(array_column($sales, 'total_amount'));
$totalPaid = array_sum(array_column($payments, 'amount'));
$balance = $totalGiven - $totalPaid;

$documents = db()->prepare('SELECT * FROM customer_documents WHERE customer_id = ? ORDER BY created_at DESC');
$documents->execute([$id]);
$documents = $documents->fetchAll();

$followups = db()->prepare('SELECT * FROM followups WHERE customer_id = ? ORDER BY follow_up_date DESC, id DESC');
$followups->execute([$id]);
$followups = $followups->fetchAll();

$waMsg = "Namaste " . $customer['name'] . ", aapka " . get_setting('company_name', APP_NAME) . " ke saath balance Rs. " . number_format($balance, 2) . " baki hai. Kripya jaldi payment kar dijiye. Dhanyawad.";

require __DIR__ . '/includes/header.php';
?>
<p><a href="<?= e(base_url($backBase . 'customers.php')) ?>">&larr; Back to customers</a></p>

<div class="card" style="display:flex;gap:20px;align-items:flex-start">
  <?php if (!empty($customer['photo'])): ?>
    <img class="customer-photo" src="<?= e(base_url($customer['photo'])) ?>">
  <?php endif; ?>
  <div style="flex:1">
    <h2 class="mt-0">
      <?= e($customer['name']) ?> <?= $customer['shop_name'] ? '('.e($customer['shop_name']).')' : '' ?>
      <?php if ($customer['status'] === 'inactive'): ?><span class="badge gray">Inactive</span><?php endif; ?>
    </h2>
    <p class="text-muted">
      <?= e($customer['place']) ?> &middot; Mobile: <?= e($customer['mobile']) ?>
      <?php if ($customer['alt_mobile']): ?> / <?= e($customer['alt_mobile']) ?><?php endif; ?>
      <?php if ($u['role'] === 'super_admin'): ?> &middot; Admin: <strong><?= e($customer['admin_name']) ?></strong><?php endif; ?>
    </p>
    <div class="action-bar">
      <a class="btn small" href="<?= e(id_url('customer_form.php', 'customer', $id)) ?>">Edit</a>
      <a class="btn small" href="<?= e(id_url_as('sale_form.php', 'customer_id', 'customer', $id)) ?>">+ New Sale / Invoice</a>
      <a class="btn small" href="<?= e(id_url_as('payment_form.php', 'customer_id', 'customer', $id)) ?>">+ Record Payment</a>
      <a class="btn small" href="<?= e(id_url_as('followup_form.php', 'customer_id', 'customer', $id)) ?>">+ Add Follow-up</a>
      <a class="btn small" href="<?= e(id_url('customer_view.php', 'customer', $id, ['statement' => 'pdf'])) ?>" target="_blank">Statement PDF</a>
      <button type="button" class="btn small wa"
        onclick="shareFileToWhatsApp('<?= e(id_url('customer_view.php', 'customer', $id, ['statement' => 'pdf'])) ?>', 'statement-<?= e(preg_replace('/\s+/', '-', $customer['name'])) ?>.pdf', '<?= e(addslashes($waMsg)) ?>', this)">
        Share Statement on WhatsApp
      </button>
      <?php if ($balance > 0): ?>
        <a class="btn small wa" target="_blank" href="<?= e(whatsapp_link($customer['mobile'], $waMsg)) ?>">Send Text Reminder</a>
      <?php endif; ?>
      <form method="post" onsubmit="return confirm('<?= $customer['status'] === 'active' ? 'Deactivate' : 'Activate' ?> this customer?')">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="toggle_status">
        <button class="btn small secondary" type="submit"><?= $customer['status'] === 'active' ? 'Deactivate' : 'Activate' ?></button>
      </form>
      <?php if (!$sales && !$payments): ?>
        <form method="post" onsubmit="return doubleConfirm('Delete this customer permanently?', 'Are you absolutely sure? This is PERMANENT and cannot be undone.')">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="delete">
          <button class="btn small danger" type="submit">Delete Customer</button>
        </form>
      <?php endif; ?>
    </div>
    <?php if ($sales || $payments): ?>
      <p class="text-muted" style="font-size:12px;margin-top:8px">Is customer ke sales/payments records hai, isliye permanently delete nahi ho sakta - "Deactivate" use karein.</p>
    <?php endif; ?>
  </div>
</div>

<div class="stat-grid">
  <div class="stat-card"><div class="label">Total Mall Diya (Sales)</div><div class="value"><?= money($totalGiven) ?></div></div>
  <div class="stat-card"><div class="label">Total Payment Aaya</div><div class="value"><?= money($totalPaid) ?></div></div>
  <div class="stat-card"><div class="label">Balance Baki</div><div class="value" style="color:<?= $balance > 0 ? '#dc2626' : '#16a34a' ?>"><?= money($balance) ?></div></div>
</div>

<div class="card">
  <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px">
    <h3 class="mt-0">Pending Invoices</h3>
    <a class="btn small secondary" href="<?= e(id_url('customer_invoice_history.php', 'customer', $id)) ?>">History (Paid Invoices)<?= $historySales ? ' (' . count($historySales) . ')' : '' ?></a>
  </div>
  <table>
    <tr><th>Date</th><th>Invoice No</th><th>Amount</th><th>Paid</th><th>Due</th><th></th></tr>
    <?php foreach ($pendingSales as $s): $paidS = (float)$s['paid_for_sale']; $dueS = max(0, (float)$s['total_amount'] - $paidS); ?>
    <tr>
      <td><?= e(date('d-M-Y', strtotime($s['sale_date']))) ?></td>
      <td><?= e($s['invoice_no']) ?></td>
      <td><?= money($s['total_amount']) ?></td>
      <td><?= $paidS > 0 ? money($paidS) : '-' ?></td>
      <td style="color:#dc2626;font-weight:600"><?= money($dueS) ?></td>
      <td><a href="<?= e(id_url('sale_view.php', 'sale', $s['id'])) ?>">View</a> &middot; <a href="<?= e(id_url('sale_form.php', 'sale', $s['id'])) ?>">Edit</a></td>
    </tr>
    <?php endforeach; ?>
    <?php if (!$pendingSales): ?><tr><td colspan="6" class="text-muted">No pending invoices.</td></tr><?php endif; ?>
  </table>
</div>

<?php
// Invoice number lookup for the Type column on each payment - tagged
// payments show the invoice they're linked to, everything else is a
// "Direct" payment against the customer's overall balance.
$invNoLookup = [];
foreach ($sales as $s) { $invNoLookup[(int)$s['id']] = $s['invoice_no']; }
?>
<div class="card">
  <h3 class="mt-0">Payments</h3>
  <table>
    <tr><th>Date</th><th>Amount</th><th>Type</th><th>Mode</th><th>Note</th><th></th></tr>
    <?php foreach ($payments as $p): ?>
    <tr>
      <td><?= e(date('d-M-Y', strtotime($p['payment_date']))) ?></td>
      <td><?= money($p['amount']) ?></td>
      <td>
        <?php if (!empty($p['sale_id']) && isset($invNoLookup[(int)$p['sale_id']])): ?>
          <span class="badge green">Invoice <?= e($invNoLookup[(int)$p['sale_id']]) ?></span>
        <?php else: ?>
          <span class="badge orange">Direct</span>
        <?php endif; ?>
      </td>
      <td><?= e($p['mode']) ?></td>
      <td><?= e($p['note']) ?></td>
      <td><a href="<?= e(id_url('payment_form.php', 'payment', $p['id'])) ?>">Edit</a></td>
    </tr>
    <?php endforeach; ?>
    <?php if (!$payments): ?><tr><td colspan="6" class="text-muted">No payments yet.</td></tr><?php endif; ?>
  </table>
</div>

<div class="card">
  <h3 class="mt-0">Follow-ups / Commitments</h3>
  <table>
    <tr><th>Date</th><th>Time</th><th>Commitment</th><th>Status</th><th>Remarks</th><th></th></tr>
    <?php foreach ($followups as $f): ?>
    <tr>
      <td><?= e(date('d-M-Y', strtotime($f['follow_up_date']))) ?></td>
      <td><?= e($f['follow_up_time'] ? date('h:i A', strtotime($f['follow_up_time'])) : '-') ?></td>
      <td><?= e($f['commitment']) ?></td>
      <td>
        <span class="badge <?= $f['status'] === 'done' ? 'green' : ($f['status'] === 'cancelled' ? 'gray' : 'orange') ?>"><?= e($f['status']) ?></span>
      </td>
      <td><?= e($f['remarks']) ?></td>
      <td>
        <?php if ($f['status'] === 'pending'): ?>
        <form method="post" action="<?= e(base_url('followup_status.php')) ?>" style="display:inline-block;margin-right:6px">
          <?= csrf_field() ?>
          <input type="hidden" name="id" value="<?= (int)$f['id'] ?>">
          <input type="hidden" name="status" value="done">
          <input type="hidden" name="return_to" value="customer_view.php?c=<?= e(sign_id('customer', $id)) ?>">
          <button class="btn small" type="submit">Done</button>
        </form>
        <?php endif; ?>
        <a href="<?= e(id_url('followup_form.php', 'followup', $f['id'])) ?>">Edit</a>
      </td>
    </tr>
    <?php endforeach; ?>
    <?php if (!$followups): ?><tr><td colspan="6" class="text-muted">No follow-ups yet.</td></tr><?php endif; ?>
  </table>
</div>

<div class="card">
  <h3 class="mt-0">Documents</h3>
  <form method="post" enctype="multipart/form-data" class="file-field" style="margin-bottom:18px">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="upload_document">
    <input type="file" name="document" accept=".pdf,.jpg,.jpeg,.png" required>
    <button class="btn small" type="submit">Upload Document</button>
  </form>
  <table>
    <tr><th>File</th><th>Uploaded</th><th></th></tr>
    <?php foreach ($documents as $d): ?>
    <tr>
      <td><a href="<?= e(base_url($d['file_path'])) ?>" target="_blank"><?= e($d['original_name']) ?></a></td>
      <td><?= e(date('d-M-Y H:i', strtotime($d['created_at']))) ?></td>
      <td>
        <form method="post" onsubmit="return confirm('Delete this document?')">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="delete_document">
          <input type="hidden" name="document_id" value="<?= (int)$d['id'] ?>">
          <button class="btn small danger" type="submit">Delete</button>
        </form>
      </td>
    </tr>
    <?php endforeach; ?>
    <?php if (!$documents): ?><tr><td colspan="3" class="text-muted">No documents uploaded.</td></tr><?php endif; ?>
  </table>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
