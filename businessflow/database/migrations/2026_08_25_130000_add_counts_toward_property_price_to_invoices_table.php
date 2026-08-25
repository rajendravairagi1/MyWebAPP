<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * An invoice linked to a property doesn't always represent money toward
 * that property's sale price — sometimes it's a separate, unrelated
 * charge (extra work the customer asked for later). This flag keeps such
 * an invoice attached to the customer/property for record-keeping while
 * excluding it from the property's collected/outstanding totals. Default
 * true so every existing invoice keeps counting exactly as it did before.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->boolean('counts_toward_property_price')->default(true)->after('unit_payment_id');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('counts_toward_property_price');
        });
    }
};
