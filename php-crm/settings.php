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
        setSetting('min_reviews_threshold', (string) max(0, (int) ($_POST['min_reviews_threshold'] ?? 10)));
        setSetting('min_rating_threshold', (string) max(0, (float) ($_POST['min_rating_threshold'] ?? 4.0)));
        flash('success', 'Settings saved.');
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
$minReviews = getSetting('min_reviews_threshold', '10');
$minRating = getSetting('min_rating_threshold', '4.0');

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
        <label>Minimum reviews threshold (below this = gap)</label>
        <input type="number" name="min_reviews_threshold" value="<?= h($minReviews) ?>">
        <label>Minimum rating threshold (below this = gap)</label>
        <input type="number" step="0.1" name="min_rating_threshold" value="<?= h($minRating) ?>">
        <button type="submit" class="btn" style="margin-top:14px;">Save Settings</button>
      </form>
    </div>
  </div>
  <div>
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
