<?php
require_once __DIR__ . '/../includes/functions.php';
require_role('super_admin');
$pageTitle = 'Company Settings';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $fields = ['company_name', 'company_phone', 'company_address', 'invoice_prefix'];
    $stmt = db()->prepare('INSERT INTO settings (setting_key, setting_value) VALUES (?,?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)');
    foreach ($fields as $f) {
        $stmt->execute([$f, trim($_POST[$f] ?? '')]);
    }
    flash('success', 'Settings updated.');
    redirect('superadmin/settings.php');
}

require __DIR__ . '/../includes/header.php';
?>
<div class="card">
  <h3 class="mt-0">Company Settings</h3>
  <form method="post">
    <?= csrf_field() ?>
    <div class="form-group"><label>Company Name</label><input name="company_name" value="<?= e(get_setting('company_name')) ?>"></div>
    <div class="form-group"><label>Phone</label><input name="company_phone" value="<?= e(get_setting('company_phone')) ?>"></div>
    <div class="form-group"><label>Address</label><textarea name="company_address" rows="2"><?= e(get_setting('company_address')) ?></textarea></div>
    <div class="form-group"><label>Invoice Prefix</label><input name="invoice_prefix" value="<?= e(get_setting('invoice_prefix')) ?>"></div>
    <button class="btn" type="submit">Save</button>
  </form>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
