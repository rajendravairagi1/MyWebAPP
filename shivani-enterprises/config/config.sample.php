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
// Full base URL of this app, no trailing slash. e.g. https://app.yourdomain.com
define('APP_URL', 'https://your-domain.com/shivani-enterprises');
// Random long string, unique per install - used to harden session cookies.
define('APP_SECRET', 'change-this-to-a-long-random-string');

// ---- Uploads ----
define('MAX_UPLOAD_MB', 5);

// ---- Timezone ----
date_default_timezone_set('Asia/Kolkata');
