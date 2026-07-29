<?php
require_once __DIR__ . '/includes/functions.php';
$u = require_login();

$fuId = resolve_id('followup');
$followup = null;
if ($fuId) {
    $stmt = db()->prepare('SELECT * FROM followups WHERE id = ?');
    $stmt->execute([$fuId]);
    $followup = $stmt->fetch();
    if (!$followup) { http_response_code(404); die('Follow-up not found.'); }
    $customerId = (int)$followup['customer_id'];
} else {
    $customerId = resolve_id_from('customer', 'customer_id');
}

$stmt = db()->prepare('SELECT * FROM customers WHERE id = ?');
$stmt->execute([$customerId]);
$customer = $stmt->fetch();
if (!$customer) { http_response_code(404); die('Customer not found.'); }
if ($u['role'] !== 'super_admin' && (int)$customer['admin_id'] !== (int)$u['id']) {
    http_response_code(403); die('Access denied.');
}
$pageTitle = $followup ? 'Edit Follow-up' : 'Add Follow-up';

if ($followup && $_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    verify_csrf();
    db()->prepare('DELETE FROM followups WHERE id = ?')->execute([$fuId]);
    flash('success', 'Follow-up deleted.');
    redirect('customer_view.php?c=' . sign_id('customer', $customerId));
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? 'save') === 'save') {
    verify_csrf();
    $commitment = trim($_POST['commitment'] ?? '');
    $date = $_POST['follow_up_date'] ?? '';
    $time = trim($_POST['follow_up_time'] ?? '') ?: null;
    $remarks = trim($_POST['remarks'] ?? '');

    if ($date === '') $errors[] = 'Follow-up date is required.';

    if (!$errors) {
        if ($followup) {
            $stmt = db()->prepare('UPDATE followups SET commitment=?, follow_up_date=?, follow_up_time=?, remarks=? WHERE id=?');
            $stmt->execute([$commitment ?: null, $date, $time, $remarks ?: null, $fuId]);
            flash('success', 'Follow-up updated.');
        } else {
            $stmt = db()->prepare('INSERT INTO followups (customer_id, admin_id, commitment, follow_up_date, follow_up_time, remarks, created_by) VALUES (?,?,?,?,?,?,?)');
            $stmt->execute([$customerId, $customer['admin_id'], $commitment ?: null, $date, $time, $remarks ?: null, $u['id']]);
            flash('success', 'Follow-up added.');
        }
        redirect('customer_view.php?c=' . sign_id('customer', $customerId));
    }
}

require __DIR__ . '/includes/header.php';
?>
<p><a href="<?= e(id_url('customer_view.php', 'customer', $customerId)) ?>">&larr; Back to <?= e($customer['name']) ?></a></p>
<div class="card">
  <h3 class="mt-0"><?= $followup ? 'Edit' : 'Add' ?> Follow-up / Commitment - <?= e($customer['name']) ?></h3>
  <?php foreach ($errors as $err): ?><div class="alert error"><?= e($err) ?></div><?php endforeach; ?>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="customer_id" value="<?= e(sign_id('customer', $customerId)) ?>">
    <div class="form-group"><label>Commitment (what was promised / discussed)</label><textarea name="commitment" rows="2" placeholder="e.g. 3 coolers @ Rs.2500 promised next week"><?= e($followup['commitment'] ?? '') ?></textarea></div>
    <div class="form-row">
      <div class="form-group"><label>Follow-up Date *</label><input type="date" name="follow_up_date" required value="<?= e($followup['follow_up_date'] ?? date('Y-m-d')) ?>"></div>
      <div class="form-group"><label>Time</label><input type="time" name="follow_up_time" value="<?= e($followup['follow_up_time'] ?? '') ?>"></div>
    </div>
    <div class="form-group"><label>Remarks</label><input name="remarks" placeholder="Optional" value="<?= e($followup['remarks'] ?? '') ?>"></div>
    <button class="btn" type="submit"><?= $followup ? 'Save Changes' : 'Save Follow-up' ?></button>
  </form>
  <?php if ($followup): ?>
    <form method="post" style="margin-top:10px" onsubmit="return confirm('Delete this follow-up? This cannot be undone.')">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="delete">
      <button class="btn small danger" type="submit">Delete Follow-up</button>
    </form>
  <?php endif; ?>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
