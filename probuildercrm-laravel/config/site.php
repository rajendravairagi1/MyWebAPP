<?php

return [
    'name' => 'Pro Builder CRM',
    'legal_name' => 'Oneweblink Pvt Ltd',
    'tagline' => 'The all-in-one CRM for real estate builders & developers',
    'short_description' => 'Pro Builder CRM helps real estate builders and developers manage projects, unit bookings, customer payments, loans, invoices, contractors and brokers — all in one place.',
    'email' => 'support@probuildercrm.com',
    'phone' => '+91 78980 02496',
    'phone_href' => '+917898002496',
    'whatsapp' => 'https://wa.me/' . env('WHATSAPP_NUMBER', '917898002496'),
    'url' => env('APP_URL', 'https://probuildercrm.com'),
    'regions' => ['India', 'USA', 'UK', 'Australia', 'Canada'],

    'nav_items' => [
        ['label' => 'Features', 'href' => '/features'],
        ['label' => 'Pricing', 'href' => '/pricing'],
        ['label' => 'Blog', 'href' => '/blog'],
        ['label' => 'FAQ', 'href' => '/faq'],
        ['label' => 'About', 'href' => '/about'],
    ],

    'footer_columns' => [
        [
            'heading' => 'Product',
            'links' => [
                ['label' => 'Features', 'href' => '/features'],
                ['label' => 'Pricing', 'href' => '/pricing'],
                ['label' => 'Blog', 'href' => '/blog'],
            ],
        ],
        [
            'heading' => 'Company',
            'links' => [
                ['label' => 'About Us', 'href' => '/about'],
                ['label' => 'FAQ', 'href' => '/faq'],
                ['label' => 'Contact', 'href' => '/contact'],
            ],
        ],
        [
            'heading' => 'Legal',
            'links' => [
                ['label' => 'Privacy Policy', 'href' => '/privacy-policy'],
                ['label' => 'Terms of Service', 'href' => '/terms-of-service'],
            ],
        ],
    ],
];
