<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pricing_plans', function (Blueprint $table) {
            // monthly_price stays the INR MRP (unchanged meaning/behaviour).
            // This holds the same MRP in other currencies, e.g.
            // {"USD": 20, "GBP": 15} — set per plan from Admin > Pricing.
            // A currency with no entry here falls back to the INR price.
            $table->json('extra_prices')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('pricing_plans', function (Blueprint $table) {
            $table->dropColumn('extra_prices');
        });
    }
};
