<?php
require_once __DIR__ . '/includes/functions.php';
$u = current_user();
if (!$u) {
    redirect('login.php');
}
redirect($u['role'] === 'super_admin' ? 'superadmin/dashboard.php' : 'admin/dashboard.php');
