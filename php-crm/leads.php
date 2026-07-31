<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';
requireLogin();

$pdo = getDb();
ensureLeadSourceColumn($pdo);

// ---------- Bulk status update ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulk_action'])) {
    verifyCsrf();
    $ids = array_filter(array_map('intval', $_POST['lead_ids'] ?? []));
    $newStatus = $_POST['bulk_status'] ?? '';
    if ($ids && in_array($newStatus, allStatuses(), true)) {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $pdo->prepare("UPDATE leads SET status = ? WHERE id IN ($placeholders)");
        $stmt->execute(array_merge([$newStatus], $ids));
        flash('success', count($ids) . ' lead(s) updated to ' . statusLabel($newStatus) . '.');
    }
    redirect('leads.php');
}

// ---------- Filters ----------
$status = $_GET['status'] ?? '';
$q = trim($_GET['q'] ?? '');
$sort = $_GET['sort'] ?? 'created_desc';
$source = $_GET['source'] ?? '';

$where = [];
$params = [];
if ($status !== '' && in_array($status, allStatuses(), true)) {
    $where[] = 'status = ?';
    $params[] = $status;
}
if ($source !== '' && in_array($source, ['google_places', 'manual'], true)) {
    $where[] = 'source = ?';
    $params[] = $source;
}
if ($q !== '') {
    $where[] = '(company_name LIKE ? OR email LIKE ? OR phone LIKE ?)';
    $params[] = "%$q%";
    $params[] = "%$q%";
    $params[] = "%$q%";
}
$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$sortMap = [
    'created_desc' => 'created_at DESC',
    'created_asc' => 'created_at ASC',
    'urgency_desc' => 'urgency_score DESC',
    'urgency_asc' => 'urgency_score ASC',
    'name_asc' => 'company_name ASC',
];
$orderSql = $sortMap[$sort] ?? $sortMap['created_desc'];

$sql = "SELECT * FROM leads $whereSql ORDER BY $orderSql LIMIT 500";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$leads = $stmt->fetchAll();

if (isset($_GET['export'])) {
    outputLeadsCsv($leads);
}

$pageTitle = 'All Leads';
require __DIR__ . '/includes/header.php';
?>
<h1>All Leads</h1>
<p class="page-subtitle"><?= count($leads) ?> lead(s) found</p>

<div class="card">
  <form method="get" class="filters">
    <div class="form-row">
      <label>Status</label>
      <select name="status">
        <option value="">All</option>
        <?php foreach (allStatuses() as $s): ?>
          <option value="<?= h($s) ?>" <?= $status === $s ? 'selected' : '' ?>><?= h(statusLabel($s)) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="form-row">
      <label>Source</label>
      <select name="source">
        <option value="">All</option>
        <option value="google_places" <?= $source === 'google_places' ? 'selected' : '' ?>>Google Places</option>
        <option value="manual" <?= $source === 'manual' ? 'selected' : '' ?>>Manual</option>
      </select>
    </div>
    <div class="form-row">
      <label>Search</label>
      <input type="text" name="q" value="<?= h($q) ?>" placeholder="Name, email, phone">
    </div>
    <div class="form-row">
      <label>Sort by</label>
      <select name="sort">
        <option value="created_desc" <?= $sort === 'created_desc' ? 'selected' : '' ?>>Newest first</option>
        <option value="created_asc" <?= $sort === 'created_asc' ? 'selected' : '' ?>>Oldest first</option>
        <option value="urgency_desc" <?= $sort === 'urgency_desc' ? 'selected' : '' ?>>Urgency (high to low)</option>
        <option value="urgency_asc" <?= $sort === 'urgency_asc' ? 'selected' : '' ?>>Urgency (low to high)</option>
        <option value="name_asc" <?= $sort === 'name_asc' ? 'selected' : '' ?>>Name (A-Z)</option>
      </select>
    </div>
    <div class="form-row"><button class="btn" type="submit">Filter</button></div>
    <div class="form-row"><a class="btn btn-outline" href="leads.php?export=1&status=<?= h($status) ?>&q=<?= urlencode($q) ?>&sort=<?= h($sort) ?>">Export CSV</a></div>
  </form>
</div>

<form method="post" id="bulk-form">
  <?= csrfField() ?>
  <div class="card">
    <div class="flex" style="margin-bottom:12px;">
      <select name="bulk_status">
        <?php foreach (allStatuses() as $s): ?>
          <option value="<?= h($s) ?>"><?= h(statusLabel($s)) ?></option>
        <?php endforeach; ?>
      </select>
      <button type="submit" name="bulk_action" value="1" class="btn btn-sm btn-secondary" onclick="return confirm('Update status for selected leads?');">Apply to selected</button>
    </div>
    <table>
      <tr>
        <th><input type="checkbox" onclick="document.querySelectorAll('.lead-check').forEach(cb=>cb.checked=this.checked)"></th>
        <th>Company</th><th>Source</th><th>Phone</th><th>Email</th><th>Status</th><th>Urgency</th><th>Last Updated</th><th></th>
      </tr>
      <?php foreach ($leads as $lead): ?>
      <?php $src = $lead['source'] ?? 'google_places'; ?>
      <tr>
        <td><input type="checkbox" class="lead-check" name="lead_ids[]" value="<?= (int) $lead['id'] ?>"></td>
        <td><?= h($lead['company_name']) ?></td>
        <td><span class="badge <?= $src === 'manual' ? 'badge-purple' : 'badge-blue' ?>"><?= $src === 'manual' ? 'Manual' : 'Google' ?></span></td>
        <td><?= h($lead['phone']) ?></td>
        <td><?= h($lead['email']) ?></td>
        <td><span class="badge <?= statusBadgeClass($lead['status']) ?>"><?= h(statusLabel($lead['status'])) ?></span></td>
        <td><?= (int) $lead['urgency_score'] ?>/5</td>
        <td><?= h($lead['updated_at']) ?></td>
        <td><a href="lead-detail.php?id=<?= (int) $lead['id'] ?>" class="btn btn-sm">Open</a></td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($leads)): ?>
      <tr><td colspan="9" class="muted">No leads found. <a href="search.php">Search &amp; Analyze</a> ya <a href="add-lead.php">Add Lead Manually</a>.</td></tr>
      <?php endif; ?>
    </table>
  </div>
</form>

<?php require __DIR__ . '/includes/footer.php'; ?>
