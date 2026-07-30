<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';
requireLogin();

$pageTitle = 'Search & Analyze';
$csrfToken = csrfToken();
require __DIR__ . '/includes/header.php';
?>
<h1>Search &amp; Analyze</h1>
<p class="page-subtitle">Search Google Places, then save promising leads straight into your database.</p>

<div class="card">
  <form id="search-form" class="flex">
    <input type="text" id="search-query" placeholder="e.g. Real estate agents in Pune" required>
    <button type="submit" class="btn">Search</button>
  </form>
</div>

<div id="search-status" class="muted small"></div>
<div id="search-results"></div>

<?php require __DIR__ . '/includes/footer.php'; ?>
<script>
  window.CSRF_TOKEN = <?= json_encode($csrfToken) ?>;
</script>
