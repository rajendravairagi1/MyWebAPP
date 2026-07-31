<?php
/** Expects $pageTitle to be set by the including page. */
$user = currentUser();
$currentFile = basename($_SERVER['SCRIPT_NAME']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= h($pageTitle ?? 'Lead CRM') ?> - Lead CRM</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?php if ($user): ?>
<div class="app-shell">
  <aside class="sidebar">
    <div class="sidebar-brand">Lead CRM</div>
    <nav class="sidebar-nav">
      <a href="dashboard.php" class="<?= $currentFile === 'dashboard.php' ? 'active' : '' ?>">Dashboard</a>
      <a href="search.php" class="<?= $currentFile === 'search.php' ? 'active' : '' ?>">Search &amp; Analyze</a>
      <a href="add-lead.php" class="<?= $currentFile === 'add-lead.php' ? 'active' : '' ?>">Add Lead Manually</a>
      <a href="leads.php" class="<?= $currentFile === 'leads.php' || $currentFile === 'lead-detail.php' ? 'active' : '' ?>">All Leads</a>
      <a href="templates.php" class="<?= $currentFile === 'templates.php' ? 'active' : '' ?>">Email Templates</a>
      <a href="settings.php" class="<?= $currentFile === 'settings.php' ? 'active' : '' ?>">Settings</a>
    </nav>
    <div class="sidebar-footer">
      <div class="sidebar-user"><?= h($user['username']) ?></div>
      <a href="logout.php" class="logout-link">Logout</a>
    </div>
  </aside>
  <main class="main-content">
    <?php $err = flash('error'); $ok = flash('success'); ?>
    <?php if ($err): ?><div class="alert alert-error"><?= h($err) ?></div><?php endif; ?>
    <?php if ($ok): ?><div class="alert alert-success"><?= h($ok) ?></div><?php endif; ?>
<?php else: ?>
<main class="main-content full">
<?php endif; ?>
