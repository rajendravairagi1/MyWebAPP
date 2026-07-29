<?php
require_once __DIR__ . '/includes/functions.php';
$u = require_login();

$id = resolve_id('customer');
$stmt = db()->prepare('SELECT * FROM customers WHERE id = ?');
$stmt->execute([$id]);
$customer = $stmt->fetch();
if (!$customer) { http_response_code(404); die('Customer not found.'); }
if ($u['role'] !== 'super_admin' && (int)$customer['admin_id'] !== (int)$u['id']) {
    http_response_code(403); die('Access denied.');
}
$pageTitle = 'Invoice History: ' . $customer['name'];

// Same "paid" rule as customer_view.php - only payments explicitly tagged
// against an invoice (payment.sale_id) count towards it being fully paid.
$sales = db()->prepare(
    "SELECT s.*, COALESCE((SELECT SUM(p.amount) FROM payments p WHERE p.sale_id = s.id), 0) AS paid_for_sale
     FROM sales s WHERE s.customer_id = ? ORDER BY s.sale_date DESC, s.id DESC"
);
$sales->execute([$id]);
$historySales = array_filter($sales->fetchAll(), function ($s) {
    return (float)$s['paid_for_sale'] >= (float)$s['total_amount'] - 0.01;
});

require __DIR__ . '/includes/header.php';
?>
<p><a href="<?= e(id_url('customer_view.php', 'customer', $id)) ?>">&larr; Back to <?= e($customer['name']) ?></a></p>
<div class="card">
  <h3 class="mt-0">History <span class="badge gray">Paid Invoices</span></h3>
  <p class="text-muted" style="font-size:13px">Jo invoice pura paid ho chuka hai (payment "against invoice" tagged hokar) wo yaha aa jaata hai — records hamesha ke liye surakshit rehte hai. Ye invoices ab "Record Payment" ki invoice list me nahi dikhengi, kyoki inhe aur payment ki zaroorat nahi hai.</p>
  <table>
    <tr><th>Date</th><th>Invoice No</th><th>Amount</th><th></th></tr>
    <?php foreach ($historySales as $s): ?>
    <tr>
      <td><?= e(date('d-M-Y', strtotime($s['sale_date']))) ?></td>
      <td><?= e($s['invoice_no']) ?></td>
      <td><?= money($s['total_amount']) ?></td>
      <td><a href="<?= e(id_url('sale_view.php', 'sale', $s['id'])) ?>">View</a> &middot; <a href="<?= e(id_url('sale_form.php', 'sale', $s['id'])) ?>">Edit</a></td>
    </tr>
    <?php endforeach; ?>
    <?php if (!$historySales): ?><tr><td colspan="4" class="text-muted">No paid invoices yet.</td></tr><?php endif; ?>
  </table>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
