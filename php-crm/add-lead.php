<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';
requireLogin();

$pdo = getDb();
ensureLeadSourceColumn($pdo);

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $companyName = trim($_POST['company_name'] ?? '');
    if ($companyName === '') {
        $error = 'Company name is required.';
    } else {
        $website = trim($_POST['website'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $fb = trim($_POST['facebook_url'] ?? '');
        $ig = trim($_POST['instagram_url'] ?? '');
        $li = trim($_POST['linkedin_url'] ?? '');

        // Optional: auto-scrape website for email/social if user checked the box
        if (!empty($_POST['auto_analyze']) && $website !== '') {
            $webInfo = analyzeWebsite($website);
            if ($email === '' && !empty($webInfo['email'])) $email = $webInfo['email'];
            if ($fb === '' && !empty($webInfo['facebook_url'])) $fb = $webInfo['facebook_url'];
            if ($ig === '' && !empty($webInfo['instagram_url'])) $ig = $webInfo['instagram_url'];
            if ($li === '' && !empty($webInfo['linkedin_url'])) $li = $webInfo['linkedin_url'];
        }

        $lead = [
            'company_name' => $companyName,
            'address' => trim($_POST['address'] ?? '') ?: null,
            'phone' => trim($_POST['phone'] ?? '') ?: null,
            'whatsapp_number' => trim($_POST['whatsapp_number'] ?? '') ?: null,
            'website' => $website ?: null,
            'email' => $email ?: null,
            'facebook_url' => $fb ?: null,
            'instagram_url' => $ig ?: null,
            'linkedin_url' => $li ?: null,
            'google_profile_url' => trim($_POST['google_profile_url'] ?? '') ?: null,
            'reviews_count' => (int) ($_POST['reviews_count'] ?? 0),
            'rating' => $_POST['rating'] !== '' ? (float) $_POST['rating'] : null,
            'industry' => trim($_POST['industry'] ?? '') ?: 'Real Estate',
            'notes' => trim($_POST['notes'] ?? '') ?: null,
            'source' => 'manual',
        ];

        $gaps = computeGaps($lead);
        $urgency = urgencyScoreFromGaps($gaps);

        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare('
                INSERT INTO leads (company_name, address, phone, whatsapp_number, website, email,
                    facebook_url, linkedin_url, instagram_url, google_profile_url,
                    reviews_count, rating, industry, notes, source, urgency_score, status)
                VALUES (:company_name, :address, :phone, :whatsapp_number, :website, :email,
                    :facebook_url, :linkedin_url, :instagram_url, :google_profile_url,
                    :reviews_count, :rating, :industry, :notes, :source, :urgency_score, "analyzed")
            ');
            $stmt->execute([
                ':company_name' => $lead['company_name'],
                ':address' => $lead['address'],
                ':phone' => $lead['phone'],
                ':whatsapp_number' => $lead['whatsapp_number'],
                ':website' => $lead['website'],
                ':email' => $lead['email'],
                ':facebook_url' => $lead['facebook_url'],
                ':linkedin_url' => $lead['linkedin_url'],
                ':instagram_url' => $lead['instagram_url'],
                ':google_profile_url' => $lead['google_profile_url'],
                ':reviews_count' => $lead['reviews_count'],
                ':rating' => $lead['rating'],
                ':industry' => $lead['industry'],
                ':notes' => $lead['notes'],
                ':source' => 'manual',
                ':urgency_score' => $urgency,
            ]);
            $leadId = (int) $pdo->lastInsertId();

            $gapStmt = $pdo->prepare('INSERT INTO lead_gaps (lead_id, gap_type, gap_detail) VALUES (?, ?, ?)');
            foreach ($gaps as $gap) {
                $gapStmt->execute([$leadId, $gap['gap_type'], $gap['gap_detail']]);
            }
            $pdo->commit();

            flash('success', 'Lead added manually. ' . count($gaps) . ' gap(s) detected.');
            redirect('lead-detail.php?id=' . $leadId);
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = 'Save failed: ' . $e->getMessage();
        }
    }
}

$pageTitle = 'Add Lead Manually';
require __DIR__ . '/includes/header.php';
?>
<h1>Add Lead Manually</h1>
<p class="page-subtitle">Bina Google Places API ke lead add karo. Same format follow karega (gaps, urgency, personalized pitch etc). Data <strong>source: manual</strong> tag ke saath save hoga taaki Google-fetched leads se alag rahe.</p>

<?php if ($error): ?><div class="alert alert-error"><?= h($error) ?></div><?php endif; ?>

<div class="card">
  <form method="post">
    <?= csrfField() ?>

    <div class="detail-grid">
      <div>
        <label>Company Name *</label>
        <input type="text" name="company_name" required value="<?= h($_POST['company_name'] ?? '') ?>">

        <label>Address</label>
        <input type="text" name="address" value="<?= h($_POST['address'] ?? '') ?>" placeholder="Street, City, State, Country">

        <label>Phone</label>
        <input type="text" name="phone" value="<?= h($_POST['phone'] ?? '') ?>">

        <label>WhatsApp Number</label>
        <input type="text" name="whatsapp_number" value="<?= h($_POST['whatsapp_number'] ?? '') ?>">

        <label>Email</label>
        <input type="email" name="email" value="<?= h($_POST['email'] ?? '') ?>">

        <label>Website</label>
        <input type="text" name="website" value="<?= h($_POST['website'] ?? '') ?>" placeholder="https://example.com">

        <label style="display:flex;align-items:center;gap:8px;font-weight:normal;margin-top:8px;">
          <input type="checkbox" name="auto_analyze" value="1" style="width:auto;" checked>
          Website ko auto-scan karo email &amp; social profiles dhundhne ke liye (recommended)
        </label>
      </div>
      <div>
        <label>Google Profile URL (GMB)</label>
        <input type="text" name="google_profile_url" value="<?= h($_POST['google_profile_url'] ?? '') ?>" placeholder="https://maps.google.com/...">

        <label>Facebook URL</label>
        <input type="text" name="facebook_url" value="<?= h($_POST['facebook_url'] ?? '') ?>">

        <label>Instagram URL</label>
        <input type="text" name="instagram_url" value="<?= h($_POST['instagram_url'] ?? '') ?>">

        <label>LinkedIn URL</label>
        <input type="text" name="linkedin_url" value="<?= h($_POST['linkedin_url'] ?? '') ?>">

        <label>Reviews Count</label>
        <input type="number" name="reviews_count" value="<?= h($_POST['reviews_count'] ?? '0') ?>" min="0">

        <label>Rating (0 to 5)</label>
        <input type="number" step="0.1" name="rating" value="<?= h($_POST['rating'] ?? '') ?>" min="0" max="5" placeholder="e.g. 4.5">

        <label>Industry</label>
        <input type="text" name="industry" value="<?= h($_POST['industry'] ?? 'Real Estate') ?>">
      </div>
    </div>

    <label>Notes</label>
    <textarea name="notes" rows="3" placeholder="Kaha se mila, kya baat hui, kuch bhi..."><?= h($_POST['notes'] ?? '') ?></textarea>

    <button type="submit" class="btn btn-success" style="margin-top:16px;">Save Lead</button>
    <a href="leads.php" class="btn btn-outline" style="margin-top:16px;">Cancel</a>
  </form>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
