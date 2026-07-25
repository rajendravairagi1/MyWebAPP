<?php
require_once __DIR__ . '/includes/functions.php';
$u = require_login();
$pageTitle = 'Add Investor Entry';

$investorId = (int)($_GET['investor_id'] ?? $_POST['investor_id'] ?? 0);
$stmt = db()->prepare('SELECT * FROM investors WHERE id = ?');
$stmt->execute([$investorId]);
$investor = $stmt->fetch();
if (!$investor) { http_response_code(404); die('Investor not found.'); }
if ((int)$investor['owner_id'] !== (int)$u['id']) {
    http_response_code(403); die('Access denied.');
}

$validTypes = ['investment', 'profit', 'payment'];
$type = in_array($_GET['type'] ?? $_POST['type'] ?? '', $validTypes, true) ? ($_GET['type'] ?? $_POST['type']) : 'investment';

$labels = [
    'investment' => 'Investment',
    'profit' => 'Profit Credited',
    'payment' => 'Payment Paid',
];

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $type = in_array($_POST['type'] ?? '', $validTypes, true) ? $_POST['type'] : 'investment';
    $amount = (float)($_POST['amount'] ?? 0);
    $date = $_POST['txn_date'] ?? date('Y-m-d');
    $note = trim($_POST['note'] ?? '');

    if ($amount <= 0) $errors[] = 'Amount must be greater than zero.';

    if (!$errors) {
        $stmt = db()->prepare('INSERT INTO investor_transactions (investor_id, type, amount, txn_date, note, created_by) VALUES (?,?,?,?,?,?)');
        $stmt->execute([$investorId, $type, $amount, $date, $note ?: null, $u['id']]);
        flash('success', $labels[$type] . ' of ' . money($amount) . ' recorded.');
        redirect('investor_view.php?id=' . $investorId);
    }
}

require __DIR__ . '/includes/header.php';
?>
<p><a href="<?= e(base_url('investor_view.php?id=' . $investorId)) ?>">&larr; Back to <?= e($investor['name']) ?></a></p>
<div class="card">
  <h3 class="mt-0">Add Entry - <?= e($investor['name']) ?></h3>
  <?php foreach ($errors as $err): ?><div class="alert error"><?= e($err) ?></div><?php endforeach; ?>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="investor_id" value="<?= (int)$investorId ?>">
    <div class="form-row">
      <div class="form-group">
        <label>Entry Type</label>
        <select name="type">
          <option value="investment" <?= $type === 'investment' ? 'selected' : '' ?>>Investment (paisa investor se aaya) - Plus</option>
          <option value="profit" <?= $type === 'profit' ? 'selected' : '' ?>>Profit Credited (investor ka profit tay hua, abhi diya nahi) - Plus</option>
          <option value="payment" <?= $type === 'payment' ? 'selected' : '' ?>>Payment Paid (investor ko cash/transfer se diya) - Minus</option>
        </select>
      </div>
      <div class="form-group"><label>Amount (Rs.) *</label><input type="number" step="0.01" min="0.01" name="amount" required></div>
      <div class="form-group"><label>Date</label><input type="date" name="txn_date" value="<?= date('Y-m-d') ?>" required></div>
    </div>
    <div class="form-group"><label>Note</label><input name="note" placeholder="Jaise: Wire bundle kharide, total profit 10000, investor ka hissa 6000"></div>
    <button class="btn" type="submit">Save Entry</button>
  </form>
  <p class="text-muted" style="font-size:13px;margin-top:10px">
    Investment aur Profit Credited dono balance me <strong>jud</strong>te hai (investor ka jama badhta hai). Payment Paid balance me se <strong>ghat</strong>ta hai (jab aap actually paisa de dete ho).
  </p>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
