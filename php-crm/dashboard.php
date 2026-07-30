<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';
requireLogin();

$pdo = getDb();

$statusCounts = array_fill_keys(allStatuses(), 0);
$stmt = $pdo->query('SELECT status, COUNT(*) AS n FROM leads GROUP BY status');
foreach ($stmt->fetchAll() as $row) {
    $statusCounts[$row['status']] = (int) $row['n'];
}
$totalLeads = array_sum($statusCounts);

$thisMonthLeads = $pdo->query("SELECT COUNT(*) AS n FROM leads WHERE created_at >= DATE_FORMAT(NOW(), '%Y-%m-01')")->fetch()['n'];
$thisMonthClosedWon = $pdo->query("SELECT COUNT(*) AS n FROM leads WHERE status = 'closed_won' AND updated_at >= DATE_FORMAT(NOW(), '%Y-%m-01')")->fetch()['n'];

$followUpNeeded = $pdo->query("
    SELECT l.* FROM leads l
    WHERE l.status = 'contacted'
    AND l.updated_at <= DATE_SUB(NOW(), INTERVAL 7 DAY)
    ORDER BY l.updated_at ASC LIMIT 10
")->fetchAll();

$recentLeads = $pdo->query('SELECT * FROM leads ORDER BY created_at DESC LIMIT 8')->fetchAll();

$pageTitle = 'Dashboard';
require __DIR__ . '/includes/header.php';
?>
<h1>Dashboard</h1>
<p class="page-subtitle">Overview of your leads pipeline</p>

<div class="stat-grid">
  <div class="stat-card"><div class="num"><?= $totalLeads ?></div><div class="label">Total Leads</div></div>
  <?php foreach (allStatuses() as $s): ?>
  <div class="stat-card"><div class="num"><?= $statusCounts[$s] ?></div><div class="label"><?= h(statusLabel($s)) ?></div></div>
  <?php endforeach; ?>
</div>

<div class="stat-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px,1fr));">
  <div class="stat-card"><div class="num"><?= $thisMonthLeads ?></div><div class="label">New Leads This Month</div></div>
  <div class="stat-card"><div class="num"><?= $thisMonthClosedWon ?></div><div class="label">Closed Won This Month</div></div>
</div>

<div class="card">
  <div class="flex-between">
    <h2 class="mt-0">Needs Follow-up (Contacted &gt; 7 days, no reply)</h2>
    <a href="leads.php?status=contacted" class="btn btn-sm btn-outline">View all contacted</a>
  </div>
  <?php if (empty($followUpNeeded)): ?>
    <p class="muted">Nothing pending. 🎉</p>
  <?php else: ?>
  <table>
    <tr><th>Company</th><th>Phone</th><th>Contacted On</th><th></th></tr>
    <?php foreach ($followUpNeeded as $lead): ?>
    <tr>
      <td><?= h($lead['company_name']) ?></td>
      <td><?= h($lead['phone']) ?></td>
      <td><?= h($lead['updated_at']) ?></td>
      <td><a href="lead-detail.php?id=<?= (int) $lead['id'] ?>" class="btn btn-sm">Open</a></td>
    </tr>
    <?php endforeach; ?>
  </table>
  <?php endif; ?>
</div>

<div class="card">
  <h2 class="mt-0">Recently Added Leads</h2>
  <table>
    <tr><th>Company</th><th>Status</th><th>Urgency</th><th>Added</th><th></th></tr>
    <?php foreach ($recentLeads as $lead): ?>
    <tr>
      <td><?= h($lead['company_name']) ?></td>
      <td><span class="badge <?= statusBadgeClass($lead['status']) ?>"><?= h(statusLabel($lead['status'])) ?></span></td>
      <td><?= (int) $lead['urgency_score'] ?>/5</td>
      <td><?= h($lead['created_at']) ?></td>
      <td><a href="lead-detail.php?id=<?= (int) $lead['id'] ?>" class="btn btn-sm">Open</a></td>
    </tr>
    <?php endforeach; ?>
  </table>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
