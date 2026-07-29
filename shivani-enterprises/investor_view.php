<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/lib/invoice_pdf.php';
$u = require_login();

$id = resolve_id('investor');
$stmt = db()->prepare('SELECT * FROM investors WHERE id = ?');
$stmt->execute([$id]);
$investor = $stmt->fetch();
if (!$investor) { http_response_code(404); die('Investor not found.'); }
if ((int)$investor['owner_id'] !== (int)$u['id']) {
    http_response_code(403); die('Access denied.');
}
$pageTitle = 'Investor: ' . $investor['name'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    verify_csrf();
    db()->prepare('DELETE FROM investors WHERE id = ?')->execute([$id]);
    flash('success', 'Investor and all their entries deleted.');
    redirect('investors.php');
}

if (($_GET['statement'] ?? '') === 'pdf') {
    $txns = db()->prepare('SELECT * FROM investor_transactions WHERE investor_id = ? ORDER BY txn_date');
    $txns->execute([$id]);
    render_investor_statement_pdf($investor, $txns->fetchAll());
    exit;
}

$txns = db()->prepare('SELECT * FROM investor_transactions WHERE investor_id = ? ORDER BY txn_date DESC, id DESC');
$txns->execute([$id]);
$txns = $txns->fetchAll();

$totalInvested = array_sum(array_map(fn($t) => $t['type'] === 'investment' ? (float)$t['amount'] : 0, $txns));
$totalProfit = array_sum(array_map(fn($t) => $t['type'] === 'profit' ? (float)$t['amount'] : 0, $txns));
$totalPaid = array_sum(array_map(fn($t) => $t['type'] === 'payment' ? (float)$t['amount'] : 0, $txns));
$balance = $totalInvested + $totalProfit - $totalPaid;

$typeLabels = ['investment' => 'Investment', 'profit' => 'Profit Credited', 'payment' => 'Payment Paid'];
$typeBadges = ['investment' => 'green', 'profit' => 'orange', 'payment' => 'red'];
$isSettled = $totalInvested > 0 && abs($balance) < 0.01;

$waMsg = "Namaste " . $investor['name'] . ", aapka " . get_setting('company_name', APP_NAME) . " ke saath investment statement - Total Investment Rs. " . number_format($totalInvested, 2) . ", Total Profit Credited Rs. " . number_format($totalProfit, 2) . ", Total Paid Rs. " . number_format($totalPaid, 2) . ", Balance Rs. " . number_format($balance, 2) . ". Dhanyawad.";

require __DIR__ . '/includes/header.php';
?>
<p><a href="<?= e(base_url('investors.php')) ?>">&larr; Back to investors</a></p>

<div class="card">
  <h2 class="mt-0"><?= e($investor['name']) ?> <?php if ($isSettled): ?><span class="badge gray">Settled - in History</span><?php endif; ?></h2>
  <p class="text-muted">
    <?php if ($investor['mobile']): ?>Mobile: <?= e($investor['mobile']) ?><?php endif; ?>
  </p>
  <?php if ($investor['notes']): ?><p class="text-muted"><?= e($investor['notes']) ?></p><?php endif; ?>
  <div class="action-bar">
    <a class="btn small" href="<?= e(id_url('investor_form.php', 'investor', $id)) ?>">Edit</a>
    <a class="btn small" href="<?= e(id_url_as('investor_txn_form.php', 'investor_id', 'investor', $id, ['type' => 'investment'])) ?>">+ Add Investment</a>
    <a class="btn small" href="<?= e(id_url_as('investor_txn_form.php', 'investor_id', 'investor', $id, ['type' => 'profit'])) ?>">+ Add Profit Credited</a>
    <a class="btn small" href="<?= e(id_url_as('investor_txn_form.php', 'investor_id', 'investor', $id, ['type' => 'payment'])) ?>">+ Add Payment Paid</a>
    <a class="btn small" href="<?= e(id_url('investor_view.php', 'investor', $id, ['statement' => 'pdf'])) ?>" target="_blank">Statement PDF</a>
    <button type="button" class="btn small wa"
      onclick="shareFileToWhatsApp('<?= e(id_url('investor_view.php', 'investor', $id, ['statement' => 'pdf'])) ?>', 'investor-statement-<?= e(preg_replace('/\s+/', '-', $investor['name'])) ?>.pdf', '<?= e(addslashes($waMsg)) ?>', this)">
      Share Statement on WhatsApp
    </button>
    <form method="post" onsubmit="return doubleConfirm('Delete this investor and ALL their investment/profit/payment entries?', 'Are you absolutely sure? This is PERMANENT and cannot be undone.')">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="delete">
      <button class="btn small danger" type="submit">Delete Investor</button>
    </form>
  </div>
</div>

<div class="stat-grid">
  <div class="stat-card"><div class="label">Total Investment</div><div class="value"><?= money($totalInvested) ?></div></div>
  <div class="stat-card"><div class="label">Total Profit Credited</div><div class="value"><?= money($totalProfit) ?></div></div>
  <div class="stat-card"><div class="label">Total Paid</div><div class="value"><?= money($totalPaid) ?></div></div>
  <div class="stat-card"><div class="label">Balance (Owed to Investor)</div><div class="value"><?= money($balance) ?></div></div>
</div>

<div class="card">
  <h3 class="mt-0">Ledger</h3>
  <table>
    <tr><th>Date</th><th>Type</th><th>Amount</th><th>Note</th><th></th></tr>
    <?php foreach ($txns as $t): ?>
    <tr>
      <td><?= e(date('d-M-Y', strtotime($t['txn_date']))) ?></td>
      <td><span class="badge <?= e($typeBadges[$t['type']] ?? 'gray') ?>"><?= e($typeLabels[$t['type']] ?? $t['type']) ?></span></td>
      <td><?= money($t['amount']) ?></td>
      <td><?= e($t['note']) ?></td>
      <td><a href="<?= e(id_url('investor_txn_form.php', 'investor_txn', $t['id'])) ?>">Edit</a></td>
    </tr>
    <?php endforeach; ?>
    <?php if (!$txns): ?><tr><td colspan="5" class="text-muted">No entries yet.</td></tr><?php endif; ?>
  </table>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
