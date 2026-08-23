<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_costs', function (Blueprint $table) {
            $table->string('bill_path')->nullable()->after('notes');
            $table->string('bill_name')->nullable()->after('bill_path');
        });
    }

    public function down(): void
    {
        Schema::table('project_costs', function (Blueprint $table) {
            $table->dropColumn(['bill_path', 'bill_name']);
        });
    }
};
