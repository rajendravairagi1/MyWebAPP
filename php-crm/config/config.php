<?php
/**
 * ============================================================
 * MAIN CONFIG - upload se pehle ye values apni hosting ke
 * hisaab se badlo (cPanel > MySQL Databases).
 * ============================================================
 */

// ---------- Database ----------
define('DB_HOST', 'localhost');
define('DB_NAME', 'your_cpanel_dbname');
define('DB_USER', 'your_cpanel_dbuser');
define('DB_PASS', 'your_cpanel_dbpassword');

// ---------- Security ----------
// Random secret, sirf tumhe pata ho. Session/CSRF ke liye use hota hai.
// Change this to any long random string before going live.
define('APP_SECRET', 'change-this-to-a-long-random-string');

// App apne aap detect kar leta hai ki request HTTPS pe hai ya nahi (ise
// waise hi chhod do - default 'true' rakho). Agar tumhari site abhi bina
// SSL (plain http://) ke chal rahi hai aur "Invalid CSRF token" / login
// baar baar fail ho raha hai, to isko 'false' kar do.
define('APP_FORCE_HTTPS_COOKIE', true);

// ---------- Timezone ----------
date_default_timezone_set('Asia/Kolkata');
