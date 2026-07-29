<?php
require_once __DIR__ . '/../includes/functions.php';
$u = require_role('admin');
$pageTitle = 'My Follow-ups / Commitments';

$status = $_GET['status'] ?? 'pending';
$where = ['f.admin_id = ?'];
$params = [$u['id']];
if ($status !== 'all') { $where[] = 'f.status = ?'; $params[] = $status; }
$whereSql = 'WHERE ' . implode(' AND ', $where);

$sql = "SELECT f.*, c.name AS customer_name, c.mobile
  FROM followups f JOIN customers c ON c.id = f.customer_id
  $whereSql ORDER BY f.follow_up_date ASC, f.id DESC LIMIT 300";
$stmt = db()->prepare($sql);
$stmt->execute($params);
$followups = $stmt->fetchAll();

require __DIR__ . '/../includes/header.php';
?>
<form class="filters card" method="get">
  <div class="form-group">
    <label>Status</label>
    <select name="status">
      <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
      <option value="done" <?= $status === 'done' ? 'selected' : '' ?>>Done</option>
      <option value="cancelled" <?= $status === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
      <option value="all" <?= $status === 'all' ? 'selected' : '' ?>>All</option>
    </select>
  </div>
  <div class="form-group"><button class="btn" type="submit">Filter</button></div>
</form>

<div class="card">
  <div class="followup-rows">
    <?php foreach ($followups as $f): $overdue = $f['status'] === 'pending' && $f['follow_up_date'] < date('Y-m-d'); ?>
    <div class="followup-row">
      <div class="followup-row-top">
        <div>
          <div class="followup-row-customer">
            <a href="<?= e(id_url('customer_view.php', 'customer', $f['customer_id'])) ?>"><?= e($f['customer_name']) ?></a>
          </div>
          <div class="followup-row-meta">
            <?= e($f['mobile']) ?><br>
            <?= e(date('d-M-Y', strtotime($f['follow_up_date']))) ?><?= $f['follow_up_time'] ? ' · ' . e(date('h:i A', strtotime($f['follow_up_time']))) : '' ?>
            <?= $overdue ? ' <span class="badge red">overdue</span>' : '' ?>
          </div>
        </div>
        <span class="badge <?= $f['status'] === 'done' ? 'green' : ($f['status'] === 'cancelled' ? 'gray' : 'orange') ?>"><?= e($f['status']) ?></span>
      </div>
      <?php if ($f['commitment']): ?><p class="followup-row-commitment"><?= e($f['commitment']) ?></p><?php endif; ?>
      <?php if ($f['remarks']): ?><p class="followup-row-remarks">Remarks: <?= e($f['remarks']) ?></p><?php endif; ?>
      <div class="followup-row-actions">
        <?php if ($f['status'] === 'pending'): ?>
        <form method="post" action="<?= e(base_url('followup_status.php')) ?>">
          <?= csrf_field() ?>
          <input type="hidden" name="id" value="<?= (int)$f['id'] ?>">
          <input type="hidden" name="status" value="done">
          <input type="hidden" name="return_to" value="admin/followups.php">
          <button class="btn small" type="submit">Mark Done</button>
        </form>
        <a class="btn small wa" target="_blank" href="<?= e(whatsapp_link($f['mobile'], 'Namaste ' . $f['customer_name'] . ', payment/order ke sambandh me follow-up. - ' . get_setting('company_name', APP_NAME))) ?>">WhatsApp</a>
        <?php endif; ?>
        <a class="btn small secondary" href="<?= e(id_url('followup_form.php', 'followup', $f['id'])) ?>">Edit</a>
      </div>
    </div>
    <?php endforeach; ?>
    <?php if (!$followups): ?><p class="text-muted">No follow-ups found.</p><?php endif; ?>
  </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
