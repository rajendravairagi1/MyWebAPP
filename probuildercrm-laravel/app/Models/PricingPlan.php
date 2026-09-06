<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricingPlan extends Model
{
    use HasFactory;

    protected $fillable = ['slug', 'name', 'description', 'monthly_price', 'highlighted', 'sort_order', 'features'];

    protected $casts = [
        'highlighted' => 'boolean',
        'features' => 'array',
    ];

    const CYCLES = [
        'monthly' => ['label' => 'Monthly', 'months' => 1, 'bonus_months' => 0],
        'half_yearly' => ['label' => '6 Months', 'months' => 6, 'bonus_months' => 1],
        'yearly' => ['label' => 'Yearly', 'months' => 12, 'bonus_months' => 2],
    ];

    /**
     * Turns this plan's admin-set monthly_price into everything the pricing
     * page displays: the real price for the given billing cycle (just
     * monthly_price times the cycle's months — no separate number to keep
     * in sync), how many bonus months a longer cycle includes, and a
     * permanent "40% off" anchor — a struck-through original price computed
     * as the real price divided by 0.6, purely a display device (the actual
     * amount charged is always the plan's own monthly_price times the cycle
     * length, exactly as configured in /admin).
     */
    public function pricingForCycle(string $cycle): array
    {
        $config = self::CYCLES[$cycle];
        $price = $this->monthly_price * $config['months'];
        $originalPrice = (int) round($price / 0.6);
        $totalMonths = $config['months'] + $config['bonus_months'];

        return [
            'months' => $config['months'],
            'bonus_months' => $config['bonus_months'],
            'total_months' => $totalMonths,
            'price' => $price,
            'original_price' => $originalPrice,
            'per_month_equivalent' => (int) round($price / $totalMonths),
        ];
    }
}
