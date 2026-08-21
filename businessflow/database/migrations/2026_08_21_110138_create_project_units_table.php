<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('unit_number');
            $table->string('type')->nullable(); // 1BHK | 2BHK | 3BHK | shop | plot | villa ...
            $table->decimal('area_sqft', 10, 2)->nullable();
            $table->decimal('price', 16, 2)->default(0);
            $table->string('status')->default('available'); // available | booked | sold
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->unique(['project_id', 'unit_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_units');
    }
};
