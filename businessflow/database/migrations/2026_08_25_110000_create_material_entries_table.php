<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('material_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_unit_id')->constrained()->cascadeOnDelete();
            $table->string('material_name');
            $table->decimal('quantity', 12, 2);
            $table->string('unit_label')->nullable(); // bags | pcs | kg | ...
            $table->string('direction'); // in | out
            $table->date('entered_on');
            $table->string('note')->nullable();
            $table->timestamps();

            $table->index(['project_unit_id', 'material_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('material_entries');
    }
};
