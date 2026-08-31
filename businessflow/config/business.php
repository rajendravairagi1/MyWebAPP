<?php

// Business types offered during onboarding (PRD §3 — Target Users & Business Types).
return [
    'types' => [
        'retailer' => 'Retailer',
        'service' => 'Service Business',
        'contractor' => 'Contractor',
        'consultant' => 'Consultant',
        'freelancer' => 'Freelancer',
        'agency' => 'Agency',
        'real_estate' => 'Real Estate',
        'repair' => 'Repair Business',
        'manufacturer' => 'Manufacturer',
        'ecommerce' => 'E-commerce',
        'restaurant' => 'Restaurant',
        'professional_services' => 'Professional Services',
        'wholesaler' => 'Small Wholesaler',
        'home_service' => 'Home-service Business',
        'other' => 'Other SME',
    ],

    'currencies' => [
        'USD' => 'US Dollar (USD)',
        'INR' => 'Indian Rupee (INR)',
        'EUR' => 'Euro (EUR)',
        'GBP' => 'British Pound (GBP)',
        'AED' => 'UAE Dirham (AED)',
        'AUD' => 'Australian Dollar (AUD)',
        'CAD' => 'Canadian Dollar (CAD)',
    ],

    // Symbols shown throughout the app in place of a hardcoded ₹ — every
    // amount is prefixed with the active business's own symbol (see
    // Business::currencySymbol()). AUD/CAD keep their country prefix so
    // they're never confused with a plain USD "$".
    'currency_symbols' => [
        'USD' => '$',
        'INR' => '₹',
        'EUR' => '€',
        'GBP' => '£',
        'AED' => 'AED ',
        'AUD' => 'A$',
        'CAD' => 'C$',
    ],
];
