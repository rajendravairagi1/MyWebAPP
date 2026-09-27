<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    | Web Push (App\Support\PushNotifier) — meeting/follow-up/payment
    | reminder alerts on a phone. Generated once via
    | Minishlink\WebPush\VAPID::createVapidKeys() and fixed forever —
    | changing these invalidates every device that already subscribed.
    */
    'web_push' => [
        'public_key' => env('VAPID_PUBLIC_KEY'),
        'private_key' => env('VAPID_PRIVATE_KEY'),
        'subject' => env('VAPID_SUBJECT', 'mailto:'.env('PLATFORM_ADMIN_EMAIL', 'rajendravairagi1@gmail.com')),
    ],

    /*
    | Google Play Billing (App\Support\GooglePlayBillingService) — lets a
    | subscription bought inside the Android (TWA) app get verified and
    | turned into a Business::plan + subscription_expires_at, the same
    | outcome the manual UPI-QR-and-admin-approval flow already produces
    | for a business paying from a browser. See PLAY_BILLING_SETUP.md for
    | every one-time step this needs in Play Console / Google Cloud
    | before any of it can work.
    |
    | service_account_path: a JSON key file, uploaded outside the public
    | webroot (storage/app/) — never commit the real file, only this path.
    |
    | product_plan_map: Play Console "Product ID" -> Business::plan. The
    | keys here MUST exactly match whatever product IDs are created in
    | Play Console; change the keys (not the values) if a different ID is
    | used there.
    */
    'google_play' => [
        'service_account_path' => env('GOOGLE_PLAY_SERVICE_ACCOUNT_PATH', storage_path('app/google-play-service-account.json')),
        'package_name' => env('ANDROID_PACKAGE_NAME'),
        'product_plan_map' => [
            'probuildercrm_solo' => 'solo',
            'probuildercrm_team' => 'team',
            'probuildercrm_company' => 'company',
        ],
        // Google Pub/Sub signs each RTDN push with a JWT it expects the
        // receiving endpoint to verify — this is the audience value we
        // configured that JWT to carry (see PLAY_BILLING_SETUP.md step
        // 7), checked in GooglePlayNotificationController before trusting
        // a payload claiming to be from Google at all.
        'rtdn_audience' => env('GOOGLE_PLAY_RTDN_AUDIENCE'),
    ],

];
