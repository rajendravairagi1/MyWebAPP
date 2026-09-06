<?php

return [
    // Single shared password that unlocks /admin (blog + pricing management).
    // Set a real value in .env before going live — /admin/login rejects
    // every password if this is left empty.
    'password' => env('ADMIN_PASSWORD'),
];
