<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // A one-off resale/trading deal on a property that isn't the
        // builder's own construction project — buy from a seller at one
        // price, sell to a buyer at another, profit is the margin. Kept
        // separate from Projects/ProjectUnit, which model the builder's
        // own developments.
        Schema::create('property_deals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('property_title');
            $table->string('address')->nullable();
            $table->string('seller_name')->nullable();
            $table->string('seller_phone')->nullable();
            $table->decimal('purchase_price', 14, 2);
            $table->string('buyer_name')->nullable();
            $table->string('buyer_phone')->nullable();
            $table->decimal('sale_price', 14, 2)->nullable();
            $table->string('status')->default('open'); // open | sold | cancelled
            $table->date('deal_date')->nullable();
            $table->date('sold_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_deals');
    }
};
