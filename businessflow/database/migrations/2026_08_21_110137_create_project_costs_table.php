<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_costs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('category'); // land | construction | material | labor | approval | marketing | other
            $table->string('description');
            $table->decimal('amount', 16, 2);
            $table->date('spent_on');
            $table->string('vendor')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['project_id', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_costs');
    }
};
