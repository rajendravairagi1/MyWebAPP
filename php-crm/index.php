<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';
redirect(currentUser() ? 'dashboard.php' : 'login.php');
