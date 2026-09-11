<?php

namespace App\Support;

/**
 * Everything about "which currency, which symbol, which country maps to
 * which currency" lives here — one place to extend the list when a new
 * country's pricing gets set up in Admin > Pricing.
 */
class Currency
{
    public const DEFAULT = 'INR';

    /**
     * Only currencies actually offered as a manual choice in the pricing
     * page's switcher and the Admin > Pricing "Other currencies" editor.
     * Adding a new one here is enough to make it selectable everywhere.
     */
    public const SYMBOLS = [
        'INR' => '₹',
        'USD' => '$',
        'GBP' => '£',
        'EUR' => '€',
        'AUD' => 'A$',
        'CAD' => 'C$',
        'AED' => 'AED ',
        'SGD' => 'S$',
    ];

    /**
     * ISO country code -> currency code, for the countries visitors
     * actually come from. Not exhaustive — anything not listed here
     * falls back to Currency::DEFAULT (INR), which is always safe since
     * every plan always has an INR price.
     */
    private const COUNTRY_CURRENCY = [
        'IN' => 'INR',
        'US' => 'USD',
        'GB' => 'GBP',
        'IE' => 'EUR',
        'DE' => 'EUR', 'FR' => 'EUR', 'ES' => 'EUR', 'IT' => 'EUR', 'NL' => 'EUR',
        'PT' => 'EUR', 'BE' => 'EUR', 'AT' => 'EUR', 'FI' => 'EUR', 'GR' => 'EUR',
        'AU' => 'AUD',
        'CA' => 'CAD',
        'AE' => 'AED',
        'SG' => 'SGD',
    ];

    public static function symbol(string $currency): string
    {
        return self::SYMBOLS[$currency] ?? self::SYMBOLS[self::DEFAULT];
    }

    public static function isSupported(string $currency): bool
    {
        return array_key_exists($currency, self::SYMBOLS);
    }

    public static function forCountry(?string $countryCode): string
    {
        if (! $countryCode) {
            return self::DEFAULT;
        }

        return self::COUNTRY_CURRENCY[strtoupper($countryCode)] ?? self::DEFAULT;
    }
}
