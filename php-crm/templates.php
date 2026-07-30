<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';
requireLogin();

$pdo = getDb();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'save') {
        $id = (int) ($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $body = trim($_POST['body'] ?? '');
        $gapTrigger = trim($_POST['gap_trigger'] ?? '');

        if ($id) {
            $stmt = $pdo->prepare('UPDATE email_templates SET name=?, subject=?, body=?, gap_trigger=? WHERE id=?');
            $stmt->execute([$name, $subject, $body, $gapTrigger, $id]);
        } else {
            $stmt = $pdo->prepare('INSERT INTO email_templates (name, subject, body, gap_trigger) VALUES (?,?,?,?)');
            $stmt->execute([$name, $subject, $body, $gapTrigger]);
        }
        flash('success', 'Template saved.');
    }

    if ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        $pdo->prepare('DELETE FROM email_templates WHERE id = ?')->execute([$id]);
        flash('success', 'Template deleted.');
    }

    redirect('templates.php');
}

$editId = (int) ($_GET['edit'] ?? 0);
$editing = null;
if ($editId) {
    $stmt = $pdo->prepare('SELECT * FROM email_templates WHERE id = ?');
    $stmt->execute([$editId]);
    $editing = $stmt->fetch();
}

$templates = $pdo->query('SELECT * FROM email_templates ORDER BY name')->fetchAll();
$gapTypes = ['no_website', 'low_reviews', 'low_rating', 'no_social', 'no_email'];

$pageTitle = 'Email Templates';
require __DIR__ . '/includes/header.php';
?>
<h1>Email Templates</h1>
<p class="page-subtitle">Reusable pitches, matched to the gap you found on a lead.</p>

<div class="detail-grid">
  <div>
    <div class="card">
      <h3 class="mt-0"><?= $editing ? 'Edit Template' : 'New Template' ?></h3>
      <form method="post">
        <?= csrfField() ?>
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id" value="<?= (int) ($editing['id'] ?? 0) ?>">
        <label>Name</label>
        <input type="text" name="name" required value="<?= h($editing['name'] ?? '') ?>">
        <label>Gap Trigger</label>
        <select name="gap_trigger">
          <option value="">-- none --</option>
          <?php foreach ($gapTypes as $g): ?>
            <option value="<?= h($g) ?>" <?= ($editing['gap_trigger'] ?? '') === $g ? 'selected' : '' ?>><?= h($g) ?></option>
          <?php endforeach; ?>
        </select>
        <label>Subject <span class="muted small">(use {{company_name}})</span></label>
        <input type="text" name="subject" required value="<?= h($editing['subject'] ?? '') ?>">
        <label>Body</label>
        <textarea name="body" rows="10" required><?= h($editing['body'] ?? '') ?></textarea>
        <button type="submit" class="btn" style="margin-top:14px;"><?= $editing ? 'Update' : 'Create' ?></button>
        <?php if ($editing): ?><a href="templates.php" class="btn btn-outline" style="margin-top:14px;">Cancel</a><?php endif; ?>
      </form>
    </div>
  </div>
  <div>
    <div class="card">
      <h3 class="mt-0">Existing Templates</h3>
      <?php foreach ($templates as $t): ?>
        <div class="history-item">
          <div class="flex-between">
            <strong><?= h($t['name']) ?></strong>
            <span class="small muted"><?= h($t['gap_trigger']) ?></span>
          </div>
          <div class="small muted"><?= h($t['subject']) ?></div>
          <div class="flex" style="margin-top:8px;">
            <a href="templates.php?edit=<?= (int) $t['id'] ?>" class="btn btn-sm btn-outline">Edit</a>
            <form method="post" onsubmit="return confirm('Delete this template?');">
              <?= csrfField() ?>
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?= (int) $t['id'] ?>">
              <button type="submit" class="btn btn-sm btn-danger">Delete</button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
      <?php if (empty($templates)): ?><p class="muted">No templates yet.</p><?php endif; ?>
    </div>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
