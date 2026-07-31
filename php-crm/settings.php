<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';
requireLogin();

$pdo = getDb();
$user = currentUser();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'save_settings') {
        setSetting('google_places_api_key', trim($_POST['google_places_api_key'] ?? ''));
        setSetting('google_places_api_enabled', isset($_POST['google_places_api_enabled']) ? '1' : '0');
        setSetting('max_reviews_threshold', (string) max(0, (int) ($_POST['max_reviews_threshold'] ?? 10)));
        setSetting('min_rating_threshold', (string) max(0, (float) ($_POST['min_rating_threshold'] ?? 4.0)));
        setSetting('max_search_results', (string) max(1, (int) ($_POST['max_search_results'] ?? 10)));
        flash('success', 'Settings saved.');
        redirect('settings.php');
    }

    if ($action === 'save_contact_info') {
        setSetting('my_name', trim($_POST['my_name'] ?? ''));
        setSetting('my_company', trim($_POST['my_company'] ?? ''));
        setSetting('my_email', trim($_POST['my_email'] ?? ''));
        setSetting('my_phone', trim($_POST['my_phone'] ?? ''));
        setSetting('my_portfolio', trim($_POST['my_portfolio'] ?? ''));
        flash('success', 'Contact info saved. Templates ab tumhare details ke saath fill honge.');
        redirect('settings.php');
    }

    if ($action === 'change_password') {
        $current = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        $stmt = $pdo->prepare('SELECT password_hash FROM users WHERE id = ?');
        $stmt->execute([$user['id']]);
        $row = $stmt->fetch();

        if (!$row || !password_verify($current, $row['password_hash'])) {
            flash('error', 'Current password is incorrect.');
        } elseif (strlen($new) < 8) {
            flash('error', 'New password must be at least 8 characters.');
        } elseif ($new !== $confirm) {
            flash('error', 'New password and confirmation do not match.');
        } else {
            $hash = password_hash($new, PASSWORD_DEFAULT);
            $pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ?')->execute([$hash, $user['id']]);
            flash('success', 'Password changed successfully.');
        }
        redirect('settings.php');
    }
}

$apiKey = getSetting('google_places_api_key');
$apiEnabled = getSetting('google_places_api_enabled', '1') === '1';
$maxReviews = getSetting('max_reviews_threshold', getSetting('min_reviews_threshold', '10'));
$minRating = getSetting('min_rating_threshold', '4.0');
$maxResults = getSetting('max_search_results', '10');
$myName = getSetting('my_name', '');
$myCompany = getSetting('my_company', '');
$myEmail = getSetting('my_email', '');
$myPhone = getSetting('my_phone', '');
$myPortfolio = getSetting('my_portfolio', '');

$pageTitle = 'Settings';
require __DIR__ . '/includes/header.php';
?>
<h1>Settings</h1>

<div class="detail-grid">
  <div>
    <div class="card">
      <h3 class="mt-0">Google Places API</h3>
      <p class="small muted">Ye Google ka paid API hai (billing account se juda hua). Jab tak "Active" ON nahi hoga, Search &amp; Analyze page kaam nahi karega aur koi charge nahi lagega.</p>
      <form method="post">
        <?= csrfField() ?>
        <input type="hidden" name="action" value="save_settings">
        <div class="flex-between" style="background:<?= $apiEnabled ? '#dcfce7' : '#fee2e2' ?>;padding:12px 14px;border-radius:8px;margin-top:14px;">
          <div>
            <strong><?= $apiEnabled ? 'API: Active' : 'API: Inactive' ?></strong>
            <div class="small muted">Search &amp; Analyze feature <?= $apiEnabled ? 'chalu' : 'band' ?> hai</div>
          </div>
          <label style="margin:0;display:flex;align-items:center;gap:8px;font-weight:normal;">
            <input type="checkbox" name="google_places_api_enabled" value="1" style="width:auto;" <?= $apiEnabled ? 'checked' : '' ?>>
            Active
          </label>
        </div>
        <label>API Key</label>
        <input type="text" name="google_places_api_key" value="<?= h($apiKey) ?>" placeholder="AIza...">
        <label>Maximum reviews (is se kam ya barabar wale hi dikhenge)</label>
        <input type="number" name="max_reviews_threshold" value="<?= h($maxReviews) ?>" min="0">
        <p class="small muted" style="margin-top:4px;">e.g. 10 &rarr; sirf wo businesses jinke reviews 10 ya usse kam hain (kamzor online presence).</p>

        <label>Minimum rating (is se zyada ya barabar wale hi dikhenge)</label>
        <input type="number" step="0.1" name="min_rating_threshold" value="<?= h($minRating) ?>" min="0" max="5">
        <p class="small muted" style="margin-top:4px;">e.g. 4.0 &rarr; sirf 4 star ya usse upar wale (achha kaam kar rahe hain, bas reviews kam).</p>

        <label>Max search results to show (API cost bachane ke liye)</label>
        <input type="number" name="max_search_results" value="<?= h($maxResults) ?>" min="1" max="20">
        <p class="small muted" style="margin-top:4px;">Testing ke liye 2 rakh sakte ho. Google har search ki cost same leta hai chahe 1 ya 20 result dikhaye - lekin "Analyze &amp; Save" click karne pe har lead ki alag Details call jaati hai, isliye kam results = kam accidental Details calls.</p>

        <button type="submit" class="btn" style="margin-top:14px;">Save Settings</button>
      </form>
    </div>
  </div>
  <div>
    <div class="card">
      <h3 class="mt-0">Your Contact Info (for email templates)</h3>
      <p class="small muted">Ye details automatically email templates mein fill honge (jaise <code>{{my_name}}</code>, <code>{{my_portfolio}}</code>). Isse har pitch personalized aur real lagta hai - AI-jaisa nahi.</p>
      <form method="post">
        <?= csrfField() ?>
        <input type="hidden" name="action" value="save_contact_info">
        <label>Your Name</label>
        <input type="text" name="my_name" value="<?= h($myName) ?>" placeholder="Rajendra Vairagi">
        <label>Your Company / Agency Name (optional)</label>
        <input type="text" name="my_company" value="<?= h($myCompany) ?>" placeholder="Oneweblink">
        <label>Your Email</label>
        <input type="email" name="my_email" value="<?= h($myEmail) ?>" placeholder="you@yourdomain.com">
        <label>Your Phone / WhatsApp</label>
        <input type="text" name="my_phone" value="<?= h($myPhone) ?>" placeholder="+91 9XXXXXXXXX">
        <label>Your Portfolio URL</label>
        <input type="text" name="my_portfolio" value="<?= h($myPortfolio) ?>" placeholder="https://yourportfolio.com">
        <button type="submit" class="btn" style="margin-top:14px;">Save Contact Info</button>
      </form>
    </div>
    <div class="card">
      <h3 class="mt-0">Change Password</h3>
      <form method="post">
        <?= csrfField() ?>
        <input type="hidden" name="action" value="change_password">
        <label>Current Password</label>
        <input type="password" name="current_password" required>
        <label>New Password</label>
        <input type="password" name="new_password" required minlength="8">
        <label>Confirm New Password</label>
        <input type="password" name="confirm_password" required minlength="8">
        <button type="submit" class="btn" style="margin-top:14px;">Change Password</button>
      </form>
    </div>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
