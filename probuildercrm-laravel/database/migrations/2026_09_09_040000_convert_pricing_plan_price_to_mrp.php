<?php

use App\Models\PricingPlan;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * monthly_price used to be the real, already-discounted price - the
     * "40% OFF" badge was a display-only number worked backward from it.
     * Now the site-wide discount % (Admin > Pricing > Offer) is what
     * determines the real price, so monthly_price needs to become the full
     * price/MRP it's discounted from. Scaling every existing value up by
     * /0.6 (the 40% this site has always run at) keeps today's displayed
     * prices unchanged the moment this ships.
     */
    public function up(): void
    {
        foreach (PricingPlan::all() as $plan) {
            $plan->update(['monthly_price' => (int) round($plan->monthly_price / 0.6)]);
        }
    }

    public function down(): void
    {
        foreach (PricingPlan::all() as $plan) {
            $plan->update(['monthly_price' => (int) round($plan->monthly_price * 0.6)]);
        }
    }
};
