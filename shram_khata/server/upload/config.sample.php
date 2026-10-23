<?php
// Copy this file to config.php and fill in your own values.
// config.php must never be shared or committed anywhere.
return [
    'db_host' => 'localhost',
    'db_name' => 'onewebli_hazribook',
    'db_user' => 'onewebli_hazribook',
    'db_pass' => 'PUT-YOUR-NEW-DATABASE-PASSWORD-HERE',

    // Email for password reset codes. Without 'smtp' PHP's mail() is used.
    'mail_from' => 'noreply@oneweblink.com',
    'mail_from_name' => 'HazriBook',
    // 'smtp' => ['host' => 'mail.oneweblink.com', 'port' => 465, 'secure' => 'ssl',
    //            'user' => 'noreply@oneweblink.com', 'pass' => 'PUT-THE-EMAIL-PASSWORD-HERE'],

    // Allow new agencies to sign up from the app.
    'allow_register' => true,

    // Server time zone for stored timestamps.
    'timezone' => 'UTC',

    // Show error details in responses. Keep false on the live server.
    'debug' => false,
];
