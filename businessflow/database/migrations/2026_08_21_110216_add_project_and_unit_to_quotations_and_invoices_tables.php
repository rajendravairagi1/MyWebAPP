<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->foreignId('project_id')->nullable()->after('customer_id')->constrained()->nullOnDelete();
            $table->foreignId('project_unit_id')->nullable()->after('project_id')->constrained('project_units')->nullOnDelete();
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('project_id')->nullable()->after('customer_id')->constrained()->nullOnDelete();
            $table->foreignId('project_unit_id')->nullable()->after('project_id')->constrained('project_units')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('project_id');
            $table->dropConstrainedForeignId('project_unit_id');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropConstrainedForeignId('project_id');
            $table->dropConstrainedForeignId('project_unit_id');
        });
    }
};
