<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricingPlan extends Model
{
    use HasFactory;

    protected $fillable = ['slug', 'name', 'description', 'monthly_price', 'extra_prices', 'highlighted', 'sort_order', 'features'];

    protected $casts = [
        'highlighted' => 'boolean',
        'features' => 'array',
        'extra_prices' => 'array',
    ];

    /**
     * The monthly MRP in a given currency — monthly_price (always INR)
     * for INR itself, the matching entry in extra_prices when one's been
     * set for that currency, or the INR price as a safe fallback when it
     * hasn't (never shows a missing/zero price).
     */
    public function priceIn(string $currency): int
    {
        if ($currency === 'INR') {
            return $this->monthly_price;
        }

        return (int) (($this->extra_prices ?? [])[$currency] ?? $this->monthly_price);
    }
}
