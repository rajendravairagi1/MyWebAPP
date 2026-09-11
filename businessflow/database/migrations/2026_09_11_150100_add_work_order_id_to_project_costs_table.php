<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_costs', function (Blueprint $table) {
            $table->foreignId('work_order_id')->nullable()->after('contractor_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('project_costs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('work_order_id');
        });
    }
};
