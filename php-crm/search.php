<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';
requireLogin();

$apiEnabled = getSetting('google_places_api_enabled', '1') === '1';
$maxReviewsCfg = (int) getSetting('max_reviews_threshold', getSetting('min_reviews_threshold', '10'));
$minRatingCfg = (float) getSetting('min_rating_threshold', '4.0');
$maxResultsCfg = (int) getSetting('max_search_results', '10');

$pageTitle = 'Search & Analyze';
$csrfToken = csrfToken();
require __DIR__ . '/includes/header.php';
?>
<h1>Search &amp; Analyze</h1>
<p class="page-subtitle">Filter: reviews &le; <strong><?= $maxReviewsCfg ?></strong> aur rating &ge; <strong><?= $minRatingCfg ?></strong>, max <strong><?= $maxResultsCfg ?></strong> results. <a href="settings.php">Change in Settings</a></p>

<?php if (!$apiEnabled): ?>
  <div class="alert alert-error">
    Google Places API is currently <strong>OFF</strong>. Go to <a href="settings.php">Settings</a> and turn it Active before searching.
  </div>
<?php endif; ?>

<div class="card">
  <form id="search-form" class="flex">
    <input type="text" id="search-query" placeholder="e.g. Real estate agents in Pune" required <?= $apiEnabled ? '' : 'disabled' ?>>
    <button type="submit" class="btn" <?= $apiEnabled ? '' : 'disabled' ?>>Search</button>
  </form>
</div>

<div id="search-status" class="muted small"></div>
<div id="search-results"></div>

<?php require __DIR__ . '/includes/footer.php'; ?>
<script>
  window.CSRF_TOKEN = <?= json_encode($csrfToken) ?>;
</script>
