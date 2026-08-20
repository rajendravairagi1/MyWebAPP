<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('sku')->nullable();
            $table->string('type')->default('product'); // product | service
            $table->string('unit')->nullable();
            $table->decimal('price', 14, 2)->default(0);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->integer('stock_qty')->nullable();
            $table->integer('low_stock_threshold')->nullable();
            $table->timestamps();

            $table->index(['business_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
