<?php
require_once __DIR__ . '/includes/functions.php';
$u = require_login();

$txnId = resolve_id('investor_txn');
$txn = null;
if ($txnId) {
    $stmt = db()->prepare('SELECT * FROM investor_transactions WHERE id = ?');
    $stmt->execute([$txnId]);
    $txn = $stmt->fetch();
    if (!$txn) { http_response_code(404); die('Entry not found.'); }
    $investorId = (int)$txn['investor_id'];
} else {
    $investorId = resolve_id_from('investor', 'investor_id');
}

$stmt = db()->prepare('SELECT * FROM investors WHERE id = ?');
$stmt->execute([$investorId]);
$investor = $stmt->fetch();
if (!$investor) { http_response_code(404); die('Investor not found.'); }
if ((int)$investor['owner_id'] !== (int)$u['id']) {
    http_response_code(403); die('Access denied.');
}
$pageTitle = $txn ? 'Edit Investor Entry' : 'Add Investor Entry';

$validTypes = ['investment', 'profit', 'payment'];
$type = in_array($_GET['type'] ?? $_POST['type'] ?? ($txn['type'] ?? ''), $validTypes, true)
    ? ($_GET['type'] ?? $_POST['type'] ?? $txn['type'])
    : 'investment';

$labels = [
    'investment' => 'Investment',
    'profit' => 'Profit Credited',
    'payment' => 'Payment Paid',
];

if ($txn && $_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    verify_csrf();
    db()->prepare('DELETE FROM investor_transactions WHERE id = ?')->execute([$txnId]);
    flash('success', 'Entry deleted.');
    redirect('investor_view.php?c=' . sign_id('investor', $investorId));
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? 'save') === 'save') {
    verify_csrf();
    $type = in_array($_POST['type'] ?? '', $validTypes, true) ? $_POST['type'] : 'investment';
    $amount = (float)($_POST['amount'] ?? 0);
    $date = $_POST['txn_date'] ?? date('Y-m-d');
    $note = trim($_POST['note'] ?? '');

    if ($amount <= 0) $errors[] = 'Amount must be greater than zero.';

    if (!$errors) {
        if ($txn) {
            $stmt = db()->prepare('UPDATE investor_transactions SET type=?, amount=?, txn_date=?, note=? WHERE id=?');
            $stmt->execute([$type, $amount, $date, $note ?: null, $txnId]);
            flash('success', $labels[$type] . ' entry updated.');
        } else {
            $stmt = db()->prepare('INSERT INTO investor_transactions (investor_id, type, amount, txn_date, note, created_by) VALUES (?,?,?,?,?,?)');
            $stmt->execute([$investorId, $type, $amount, $date, $note ?: null, $u['id']]);
            flash('success', $labels[$type] . ' of ' . money($amount) . ' recorded.');
        }
        redirect('investor_view.php?c=' . sign_id('investor', $investorId));
    }
}

require __DIR__ . '/includes/header.php';
?>
<p><a href="<?= e(id_url('investor_view.php', 'investor', $investorId)) ?>">&larr; Back to <?= e($investor['name']) ?></a></p>
<div class="card">
  <h3 class="mt-0"><?= $txn ? 'Edit Entry' : 'Add Entry' ?> - <?= e($investor['name']) ?></h3>
  <?php foreach ($errors as $err): ?><div class="alert error"><?= e($err) ?></div><?php endforeach; ?>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="investor_id" value="<?= e(sign_id('investor', $investorId)) ?>">
    <div class="form-row">
      <div class="form-group">
        <label>Entry Type</label>
        <select name="type">
          <option value="investment" <?= $type === 'investment' ? 'selected' : '' ?>>Investment (paisa investor se aaya) - Plus</option>
          <option value="profit" <?= $type === 'profit' ? 'selected' : '' ?>>Profit Credited (investor ka profit tay hua, abhi diya nahi) - Plus</option>
          <option value="payment" <?= $type === 'payment' ? 'selected' : '' ?>>Payment Paid (investor ko cash/transfer se diya) - Minus</option>
        </select>
      </div>
      <div class="form-group"><label>Amount (Rs.) *</label><input type="number" step="0.01" min="0.01" name="amount" required value="<?= e($txn['amount'] ?? '') ?>"></div>
      <div class="form-group"><label>Date</label><input type="date" name="txn_date" value="<?= e($txn['txn_date'] ?? date('Y-m-d')) ?>" required></div>
    </div>
    <div class="form-group"><label>Note</label><input name="note" placeholder="Jaise: Wire bundle kharide, total profit 10000, investor ka hissa 6000" value="<?= e($txn['note'] ?? '') ?>"></div>
    <button class="btn" type="submit"><?= $txn ? 'Save Changes' : 'Save Entry' ?></button>
  </form>
  <?php if ($txn): ?>
    <form method="post" style="margin-top:10px" onsubmit="return confirm('Delete this entry? This cannot be undone.')">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="delete">
      <button class="btn small danger" type="submit">Delete Entry</button>
    </form>
  <?php endif; ?>
  <p class="text-muted" style="font-size:13px;margin-top:10px">
    Investment aur Profit Credited dono balance me <strong>jud</strong>te hai (investor ka jama badhta hai). Payment Paid balance me se <strong>ghat</strong>ta hai (jab aap actually paisa de dete ho).
  </p>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
