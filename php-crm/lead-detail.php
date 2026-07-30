<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';
requireLogin();

$pdo = getDb();
$id = (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare('SELECT * FROM leads WHERE id = ?');
$stmt->execute([$id]);
$lead = $stmt->fetch();
if (!$lead) {
    flash('error', 'Lead not found.');
    redirect('leads.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'update_lead') {
        $stmt = $pdo->prepare('UPDATE leads SET whatsapp_number = ?, notes = ?, status = ? WHERE id = ?');
        $stmt->execute([
            trim($_POST['whatsapp_number'] ?? ''),
            trim($_POST['notes'] ?? ''),
            in_array($_POST['status'] ?? '', allStatuses(), true) ? $_POST['status'] : $lead['status'],
            $id,
        ]);
        flash('success', 'Lead updated.');
        redirect('lead-detail.php?id=' . $id);
    }

    if ($action === 'mark_contacted') {
        $templateId = (int) ($_POST['template_id'] ?? 0) ?: null;
        $subject = trim($_POST['subject'] ?? '');
        $body = trim($_POST['body'] ?? '');

        $ins = $pdo->prepare('INSERT INTO email_log (lead_id, template_id, subject, body, reply_status) VALUES (?, ?, ?, ?, "pending")');
        $ins->execute([$id, $templateId, $subject, $body]);

        $pdo->prepare("UPDATE leads SET status = 'contacted' WHERE id = ?")->execute([$id]);
        flash('success', 'Marked as Contacted. Email logged.');
        redirect('lead-detail.php?id=' . $id);
    }

    if ($action === 'mark_replied') {
        $replyNotes = trim($_POST['reply_notes'] ?? '');
        $emailLogId = (int) ($_POST['email_log_id'] ?? 0);

        if ($emailLogId) {
            $upd = $pdo->prepare("UPDATE email_log SET reply_status = 'replied', reply_received_at = NOW(), reply_notes = ? WHERE id = ? AND lead_id = ?");
            $upd->execute([$replyNotes, $emailLogId, $id]);
        }
        $pdo->prepare("UPDATE leads SET status = 'replied' WHERE id = ?")->execute([$id]);
        flash('success', 'Marked as Replied.');
        redirect('lead-detail.php?id=' . $id);
    }
}

$gaps = $pdo->prepare('SELECT * FROM lead_gaps WHERE lead_id = ?');
$gaps->execute([$id]);
$gaps = $gaps->fetchAll();

$emailHistory = $pdo->prepare('SELECT * FROM email_log WHERE lead_id = ? ORDER BY sent_at DESC');
$emailHistory->execute([$id]);
$emailHistory = $emailHistory->fetchAll();

$latestPendingEmail = null;
foreach ($emailHistory as $e) {
    if ($e['reply_status'] === 'pending') { $latestPendingEmail = $e; break; }
}

$templates = $pdo->query('SELECT * FROM email_templates ORDER BY name')->fetchAll();

$pageTitle = h($lead['company_name']);
require __DIR__ . '/includes/header.php';
?>
<div class="flex-between">
  <h1><?= h($lead['company_name']) ?></h1>
  <span class="badge <?= statusBadgeClass($lead['status']) ?>"><?= h(statusLabel($lead['status'])) ?></span>
</div>
<p class="page-subtitle">Urgency score: <?= (int) $lead['urgency_score'] ?>/5 &middot; Added <?= h($lead['created_at']) ?></p>

<div class="detail-grid">
  <div>
    <div class="card">
      <h3 class="mt-0">Company Info</h3>
      <p><strong>Address:</strong> <?= h($lead['address']) ?: '<span class="muted">-</span>' ?></p>
      <p><strong>Phone:</strong> <?= h($lead['phone']) ?: '<span class="muted">-</span>' ?></p>
      <p><strong>WhatsApp:</strong> <?= h($lead['whatsapp_number']) ?: '<span class="muted">-</span>' ?></p>
      <p><strong>Email:</strong> <?= h($lead['email']) ?: '<span class="muted">-</span>' ?></p>
      <p><strong>Website:</strong> <?php if ($lead['website']): ?><a href="<?= h($lead['website']) ?>" target="_blank" rel="noopener"><?= h($lead['website']) ?></a><?php else: ?><span class="muted">-</span><?php endif; ?></p>
      <p><strong>Google Profile:</strong> <?php if ($lead['google_profile_url']): ?><a href="<?= h($lead['google_profile_url']) ?>" target="_blank" rel="noopener">View on Google</a><?php else: ?><span class="muted">-</span><?php endif; ?></p>
      <p><strong>Social:</strong>
        <?php foreach (['facebook_url' => 'Facebook', 'linkedin_url' => 'LinkedIn', 'instagram_url' => 'Instagram'] as $key => $label): ?>
          <?php if ($lead[$key]): ?><a href="<?= h($lead[$key]) ?>" target="_blank" rel="noopener"><?= $label ?></a> &nbsp; <?php endif; ?>
        <?php endforeach; ?>
        <?php if (!$lead['facebook_url'] && !$lead['linkedin_url'] && !$lead['instagram_url']): ?><span class="muted">None found</span><?php endif; ?>
      </p>
      <p><strong>Reviews:</strong> <?= (int) $lead['reviews_count'] ?> &middot; <strong>Rating:</strong> <?= h($lead['rating'] ?: 'N/A') ?></p>
    </div>

    <div class="card">
      <h3 class="mt-0">Gaps Found</h3>
      <?php if (empty($gaps)): ?>
        <p class="muted">No gaps detected.</p>
      <?php else: ?>
        <?php foreach ($gaps as $g): ?>
          <span class="gap-tag"><?= h($g['gap_detail']) ?></span>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <div class="card">
      <h3 class="mt-0">Send / Log Email</h3>
      <form method="post">
        <?= csrfField() ?>
        <input type="hidden" name="action" value="mark_contacted">
        <label>Template</label>
        <select name="template_id" id="template-select">
          <option value="">-- Choose a template --</option>
          <?php foreach ($templates as $t): ?>
            <option value="<?= (int) $t['id'] ?>" data-subject="<?= h($t['subject']) ?>" data-body="<?= h($t['body']) ?>"><?= h($t['name']) ?></option>
          <?php endforeach; ?>
        </select>
        <label>Subject</label>
        <input type="text" name="subject" id="email-subject" value="">
        <label>Body (copy this into your Gmail/Outlook, then click Mark as Contacted)</label>
        <textarea name="body" id="email-body" rows="8"></textarea>
        <button type="submit" class="btn btn-success" style="margin-top:14px;">Mark as Contacted</button>
      </form>
    </div>

    <?php if ($latestPendingEmail): ?>
    <div class="card">
      <h3 class="mt-0">Mark Reply Received</h3>
      <form method="post">
        <?= csrfField() ?>
        <input type="hidden" name="action" value="mark_replied">
        <input type="hidden" name="email_log_id" value="<?= (int) $latestPendingEmail['id'] ?>">
        <label>What did they say? (notes)</label>
        <textarea name="reply_notes" rows="4"></textarea>
        <button type="submit" class="btn btn-secondary" style="margin-top:14px;">Mark as Replied</button>
      </form>
    </div>
    <?php endif; ?>

    <div class="card">
      <h3 class="mt-0">Email History</h3>
      <?php if (empty($emailHistory)): ?>
        <p class="muted">No emails logged yet.</p>
      <?php else: ?>
        <?php foreach ($emailHistory as $e): ?>
          <div class="history-item">
            <strong><?= h($e['subject']) ?></strong>
            <span class="badge <?= $e['reply_status'] === 'replied' ? 'badge-purple' : 'badge-gray' ?>"><?= h(ucfirst($e['reply_status'])) ?></span>
            <div class="small muted">Sent: <?= h($e['sent_at']) ?><?= $e['reply_received_at'] ? ' &middot; Replied: ' . h($e['reply_received_at']) : '' ?></div>
            <?php if ($e['reply_notes']): ?><div class="small">Notes: <?= h($e['reply_notes']) ?></div><?php endif; ?>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

  <div>
    <div class="card">
      <h3 class="mt-0">Status &amp; Notes</h3>
      <form method="post">
        <?= csrfField() ?>
        <input type="hidden" name="action" value="update_lead">
        <label>Status</label>
        <select name="status">
          <?php foreach (allStatuses() as $s): ?>
            <option value="<?= h($s) ?>" <?= $lead['status'] === $s ? 'selected' : '' ?>><?= h(statusLabel($s)) ?></option>
          <?php endforeach; ?>
        </select>
        <label>WhatsApp Number</label>
        <input type="text" name="whatsapp_number" value="<?= h($lead['whatsapp_number']) ?>">
        <label>Notes</label>
        <textarea name="notes" rows="6"><?= h($lead['notes']) ?></textarea>
        <button type="submit" class="btn" style="margin-top:14px;">Save</button>
      </form>
    </div>
  </div>
</div>

<script>
  var companyName = <?= json_encode($lead['company_name']) ?>;
  document.getElementById('template-select').addEventListener('change', function () {
    var opt = this.options[this.selectedIndex];
    var subject = (opt.dataset.subject || '').split('{{company_name}}').join(companyName);
    var body = (opt.dataset.body || '').split('{{company_name}}').join(companyName);
    document.getElementById('email-subject').value = subject;
    document.getElementById('email-body').value = body;
  });
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
