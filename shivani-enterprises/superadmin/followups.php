<?php
require_once __DIR__ . '/../includes/functions.php';
require_role('super_admin');
$pageTitle = 'Follow-ups / Commitments (All Admins)';

$adminFilter = (int)($_GET['admin_id'] ?? 0);
$status = $_GET['status'] ?? 'pending';

$where = [];
$params = [];
if ($adminFilter) { $where[] = 'f.admin_id = ?'; $params[] = $adminFilter; }
if ($status !== 'all') { $where[] = 'f.status = ?'; $params[] = $status; }
$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$sql = "SELECT f.*, c.name AS customer_name, c.mobile, u.name AS admin_name
  FROM followups f JOIN customers c ON c.id = f.customer_id JOIN users u ON u.id = f.admin_id
  $whereSql ORDER BY f.follow_up_date ASC, f.id DESC LIMIT 300";
$stmt = db()->prepare($sql);
$stmt->execute($params);
$followups = $stmt->fetchAll();

$admins = db()->query('SELECT id, name FROM users WHERE role = "admin" ORDER BY name')->fetchAll();

require __DIR__ . '/../includes/header.php';
?>
<form class="filters card" method="get">
  <div class="form-group">
    <label>Admin</label>
    <select name="admin_id">
      <option value="0">All</option>
      <?php foreach ($admins as $a): ?><option value="<?= (int)$a['id'] ?>" <?= $adminFilter === (int)$a['id'] ? 'selected' : '' ?>><?= e($a['name']) ?></option><?php endforeach; ?>
    </select>
  </div>
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
  <table>
    <tr><th>Date</th><th>Time</th><th>Customer</th><th>Admin</th><th>Commitment</th><th>Status</th><th></th></tr>
    <?php foreach ($followups as $f): $overdue = $f['status'] === 'pending' && $f['follow_up_date'] < date('Y-m-d'); ?>
    <tr>
      <td><?= e(date('d-M-Y', strtotime($f['follow_up_date']))) ?> <?= $overdue ? '<span class="badge red">overdue</span>' : '' ?></td>
      <td><?= e($f['follow_up_time'] ? date('h:i A', strtotime($f['follow_up_time'])) : '-') ?></td>
      <td><a href="<?= e(base_url('customer_view.php?id=' . $f['customer_id'])) ?>"><?= e($f['customer_name']) ?></a> (<?= e($f['mobile']) ?>)</td>
      <td><?= e($f['admin_name']) ?></td>
      <td><?= e($f['commitment']) ?></td>
      <td><span class="badge <?= $f['status'] === 'done' ? 'green' : ($f['status'] === 'cancelled' ? 'gray' : 'orange') ?>"><?= e($f['status']) ?></span></td>
      <td>
        <?php if ($f['status'] === 'pending'): ?>
        <form method="post" action="<?= e(base_url('followup_status.php')) ?>" style="display:inline">
          <?= csrf_field() ?>
          <input type="hidden" name="id" value="<?= (int)$f['id'] ?>">
          <input type="hidden" name="status" value="done">
          <input type="hidden" name="return_to" value="superadmin/followups.php">
          <button class="btn small" type="submit">Mark Done</button>
        </form>
        <?php endif; ?>
        <a class="btn small secondary" href="<?= e(base_url('followup_form.php?id=' . $f['id'])) ?>">Edit</a>
      </td>
    </tr>
    <?php endforeach; ?>
    <?php if (!$followups): ?><tr><td colspan="7" class="text-muted">No follow-ups found.</td></tr><?php endif; ?>
  </table>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
