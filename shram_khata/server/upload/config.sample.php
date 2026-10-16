<?php
// Copy this file to config.php and fill in your own values.
// config.php must never be shared or committed anywhere.
return [
    'db_host' => 'localhost',
    'db_name' => 'onewebli_hazribook',
    'db_user' => 'onewebli_hazribook',
    'db_pass' => 'PUT-YOUR-NEW-DATABASE-PASSWORD-HERE',

    // Allow new agencies to sign up from the app.
    'allow_register' => true,

    // Server time zone for stored timestamps.
    'timezone' => 'UTC',

    // Show error details in responses. Keep false on the live server.
    'debug' => false,
];
