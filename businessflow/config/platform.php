<?php

// Platform-owner settings — this is the reseller's own account (you),
// not a per-tenant business setting. Override via .env if you ever need
// to change these without redeploying code.
return [
    'admin_email' => env('PLATFORM_ADMIN_EMAIL', 'rajendravairagi1@gmail.com'),

    'demo_email' => env('PLATFORM_DEMO_EMAIL', 'demo@businessflow.local'),
];
