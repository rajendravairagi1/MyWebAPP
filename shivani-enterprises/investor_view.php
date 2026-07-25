<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/lib/invoice_pdf.php';
$u = require_login();

$id = (int)($_GET['id'] ?? 0);
$stmt = db()->prepare('SELECT * FROM investors WHERE id = ?');
$stmt->execute([$id]);
$investor = $stmt->fetch();
if (!$investor) { http_response_code(404); die('Investor not found.'); }
if ((int)$investor['owner_id'] !== (int)$u['id']) {
    http_response_code(403); die('Access denied.');
}
$pageTitle = 'Investor: ' . $investor['name'];

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
$totalPaid = array_sum(array_map(fn($t) => $t['type'] === 'payout' ? (float)$t['amount'] : 0, $txns));
$net = $totalInvested - $totalPaid;

$waMsg = "Namaste " . $investor['name'] . ", aapka " . get_setting('company_name', APP_NAME) . " ke saath investment statement - Total Investment Rs. " . number_format($totalInvested, 2) . ", Total Profit Paid Rs. " . number_format($totalPaid, 2) . ". Dhanyawad.";

require __DIR__ . '/includes/header.php';
?>
<p><a href="<?= e(base_url('investors.php')) ?>">&larr; Back to investors</a></p>

<div class="card">
  <h2 class="mt-0"><?= e($investor['name']) ?></h2>
  <p class="text-muted">
    <?php if ($investor['mobile']): ?>Mobile: <?= e($investor['mobile']) ?><?php endif; ?>
  </p>
  <?php if ($investor['notes']): ?><p class="text-muted"><?= e($investor['notes']) ?></p><?php endif; ?>
  <div class="action-bar">
    <a class="btn small" href="<?= e(base_url('investor_form.php?id=' . $id)) ?>">Edit</a>
    <a class="btn small" href="<?= e(base_url('investor_txn_form.php?investor_id=' . $id . '&type=investment')) ?>">+ Add Investment</a>
    <a class="btn small" href="<?= e(base_url('investor_txn_form.php?investor_id=' . $id . '&type=payout')) ?>">+ Add Profit Payout</a>
    <a class="btn small" href="<?= e(base_url('investor_view.php?id=' . $id . '&statement=pdf')) ?>" target="_blank">Statement PDF</a>
    <button type="button" class="btn small wa"
      onclick="shareFileToWhatsApp('<?= e(base_url('investor_view.php?id=' . $id . '&statement=pdf')) ?>', 'investor-statement-<?= e(preg_replace('/\s+/', '-', $investor['name'])) ?>.pdf', '<?= e(addslashes($waMsg)) ?>', this)">
      Share Statement on WhatsApp
    </button>
  </div>
</div>

<div class="stat-grid">
  <div class="stat-card"><div class="label">Total Investment</div><div class="value"><?= money($totalInvested) ?></div></div>
  <div class="stat-card"><div class="label">Total Profit Paid</div><div class="value"><?= money($totalPaid) ?></div></div>
  <div class="stat-card"><div class="label">Net</div><div class="value"><?= money($net) ?></div></div>
</div>

<div class="card">
  <h3 class="mt-0">Ledger</h3>
  <table>
    <tr><th>Date</th><th>Type</th><th>Amount</th><th>Note</th></tr>
    <?php foreach ($txns as $t): ?>
    <tr>
      <td><?= e(date('d-M-Y', strtotime($t['txn_date']))) ?></td>
      <td><span class="badge <?= $t['type'] === 'investment' ? 'green' : 'orange' ?>"><?= $t['type'] === 'investment' ? 'Investment' : 'Profit Paid' ?></span></td>
      <td><?= money($t['amount']) ?></td>
      <td><?= e($t['note']) ?></td>
    </tr>
    <?php endforeach; ?>
    <?php if (!$txns): ?><tr><td colspan="4" class="text-muted">No entries yet.</td></tr><?php endif; ?>
  </table>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
