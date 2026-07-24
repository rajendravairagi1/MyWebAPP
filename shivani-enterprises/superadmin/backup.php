<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/backup.php';
require_role('super_admin');
$pageTitle = 'Backup';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'run_backup') {
    verify_csrf();
    try {
        $file = run_backup();
        log_activity(current_user()['id'], 'manual_backup', $file);
        flash('success', 'Backup created: ' . $file);
    } catch (Throwable $e) {
        flash('error', 'Backup failed: ' . $e->getMessage());
    }
    redirect('superadmin/backup.php');
}

if (isset($_GET['download'])) {
    $name = basename($_GET['download']);
    $path = __DIR__ . '/../backups/' . $name;
    if (preg_match('/^backup-[\d_-]+\.sql$/', $name) && file_exists($path)) {
        header('Content-Type: application/sql');
        header('Content-Disposition: attachment; filename="' . $name . '"');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;
    }
    http_response_code(404);
    die('Backup file not found.');
}

$backups = list_backups();
require __DIR__ . '/../includes/header.php';
?>
<div class="card">
  <h3 class="mt-0">Database Backup</h3>
  <p class="text-muted">Backups are plain SQL dumps of the whole database. Keep the last <?= BACKUP_KEEP ?> automatically; older ones are deleted.</p>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="run_backup">
    <button class="btn" type="submit">Run Backup Now</button>
  </form>
</div>

<div class="card">
  <h3 class="mt-0">Set up Daily Automatic Backup (recommended)</h3>
  <p>In cPanel, go to <strong>Advanced &rarr; Cron Jobs</strong> and add a job that runs once every day, e.g. at 2:00 AM, running:</p>
  <pre style="background:#f3f4f6;padding:10px;border-radius:6px;overflow:auto">php <?= e(realpath(__DIR__ . '/../cron/daily_backup.php') ?: __DIR__ . '/../cron/daily_backup.php') ?></pre>
  <p class="text-muted">If your host only lets you enter a URL-based cron, use a "Cron Job -> URL" style job pointing to <code>cron/daily_backup.php?token=YOUR_APP_SECRET</code> instead (see file comments).</p>
</div>

<div class="card">
  <h3 class="mt-0">Existing Backups</h3>
  <table>
    <tr><th>File</th><th>Size</th><th>Created</th><th></th></tr>
    <?php foreach ($backups as $b): ?>
    <tr>
      <td><?= e($b['name']) ?></td>
      <td><?= number_format($b['size'] / 1024, 1) ?> KB</td>
      <td><?= e(date('d-M-Y H:i', $b['time'])) ?></td>
      <td><a href="<?= e(base_url('superadmin/backup.php?download=' . urlencode($b['name']))) ?>">Download</a></td>
    </tr>
    <?php endforeach; ?>
    <?php if (!$backups): ?><tr><td colspan="4" class="text-muted">No backups yet.</td></tr><?php endif; ?>
  </table>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
