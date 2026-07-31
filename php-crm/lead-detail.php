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

// Rich placeholder map used by every template + the personalized pitch.
$templateVars = buildTemplateVars($lead, $gaps);

// A ready-made personalized pitch that mentions the lead's actual rating,
// reviews, website, GMB link, and what's missing - so it reads like a real
// human wrote it, not an AI blast.
$autoPitchSubject = "Quick idea for {{company_name}} - noticed something on your Google profile";
$autoPitchBody =
"Hi {{company_name}} team,\n\n" .
"I was researching real estate businesses in your area and came across your Google Business listing. You have a {{rating}}-star rating from {{reviews_count}} reviews - clearly you're doing quality work.\n\n" .
"Your website: {{website_line}}\n" .
"Your Google profile: {{google_profile_url}}\n\n" .
"I noticed a few areas where a small fix could bring in significantly more customers:\n\n" .
"{{gaps_list}}\n\n" .
"For local businesses, these gaps often mean losing customers to competitors who show up higher on Google Maps. I help fix exactly this - build/upgrade websites, collect more genuine reviews, and get your business seen more on Google.\n\n" .
"If you'd like to see the kind of work I've done for similar businesses: {{my_portfolio}}\n\n" .
"Would you be open to a quick 10-min chat this week? No pitch - just showing you what could work for {{company_name}}.\n\n" .
"Best,\n" .
"{{my_name}}\n" .
"{{my_company}}\n" .
"{{my_phone}} | {{my_email}}";

// Helpful Google search URLs for social media when we couldn't auto-detect.
$companyForSearch = urlencode('"' . $lead['company_name'] . '"');
$searchLinks = [
    'Facebook' => "https://www.google.com/search?q=site%3Afacebook.com+$companyForSearch",
    'Instagram' => "https://www.google.com/search?q=site%3Ainstagram.com+$companyForSearch",
    'LinkedIn' => "https://www.google.com/search?q=site%3Alinkedin.com+$companyForSearch",
];

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
        <?php if (!$lead['facebook_url'] && !$lead['linkedin_url'] && !$lead['instagram_url']): ?><span class="muted">None auto-detected</span><?php endif; ?>
      </p>
      <p class="small muted" style="margin-top:-6px;">Not auto-detected? Manually search Google:
        <?php foreach ($searchLinks as $label => $url): ?>
          <a href="<?= h($url) ?>" target="_blank" rel="noopener"><?= h($label) ?></a><?= $label !== 'LinkedIn' ? ' &middot; ' : '' ?>
        <?php endforeach; ?>
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
      <?php $missingContact = !getSetting('my_name') || !getSetting('my_portfolio'); ?>
      <?php if ($missingContact): ?>
        <div class="alert alert-error">Tip: <a href="settings.php">Settings</a> mein apna Name aur Portfolio URL daal do - phir templates aur auto-pitch automatically fill honge.</div>
      <?php endif; ?>
      <div class="flex" style="margin-bottom:10px;">
        <button type="button" class="btn btn-secondary btn-sm" onclick="fillAutoPitch()">Generate Personalized Pitch</button>
        <span class="small muted">Rating, reviews, website, gaps &mdash; sab actual data ke saath fill hoga.</span>
      </div>
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
        <textarea name="body" id="email-body" rows="12"></textarea>
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
  var TEMPLATE_VARS = <?= json_encode($templateVars) ?>;
  var AUTO_PITCH_SUBJECT = <?= json_encode($autoPitchSubject) ?>;
  var AUTO_PITCH_BODY = <?= json_encode($autoPitchBody) ?>;

  function renderTemplate(str) {
    return (str || '').replace(/\{\{\s*(\w+)\s*\}\}/g, function (match, key) {
      return TEMPLATE_VARS.hasOwnProperty(key) ? TEMPLATE_VARS[key] : match;
    });
  }

  document.getElementById('template-select').addEventListener('change', function () {
    var opt = this.options[this.selectedIndex];
    document.getElementById('email-subject').value = renderTemplate(opt.dataset.subject || '');
    document.getElementById('email-body').value = renderTemplate(opt.dataset.body || '');
  });

  function fillAutoPitch() {
    document.getElementById('email-subject').value = renderTemplate(AUTO_PITCH_SUBJECT);
    document.getElementById('email-body').value = renderTemplate(AUTO_PITCH_BODY);
    document.getElementById('template-select').value = '';
  }
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
