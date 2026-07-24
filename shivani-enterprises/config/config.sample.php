<?php
/**
 * Copy this file to config.php and fill in your real hosting details.
 * config.php is gitignored / should NEVER be shared or committed with real
 * credentials in it.
 */

// ---- Database (cPanel: MySQL Databases section gives you these) ----
define('DB_HOST', 'localhost');
define('DB_NAME', 'yourcpaneluser_shivani');
define('DB_USER', 'yourcpaneluser_dbuser');
define('DB_PASS', 'your-db-password');

// ---- App ----
define('APP_NAME', 'Shivani Enterprises');
// Full base URL of this app, no trailing slash.
define('APP_URL', 'https://shivani.oneweblink.com');
// Random long string, unique per install - used to harden session cookies
// and to gate the URL-based backup cron trigger. Already randomly generated
// for this install - you don't need to change it, just keep it secret.
define('APP_SECRET', '0e26ce0cdfa15408d2f4fa7d5b78075167e9f8fa7e94380c24c9d72dd358c0b6');

// ---- Uploads ----
define('MAX_UPLOAD_MB', 5);

// ---- Timezone ----
date_default_timezone_set('Asia/Kolkata');
