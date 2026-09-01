<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('property_deals', function (Blueprint $table) {
            $table->foreignId('broker_id')->nullable()->after('property_title')->constrained()->nullOnDelete();
        });

        Schema::table('project_units', function (Blueprint $table) {
            $table->foreignId('broker_id')->nullable()->after('customer_id')->constrained()->nullOnDelete();
        });

        Schema::table('broker_transactions', function (Blueprint $table) {
            $table->foreignId('property_deal_id')->nullable()->after('project_unit_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('property_deals', function (Blueprint $table) {
            $table->dropConstrainedForeignId('broker_id');
        });

        Schema::table('project_units', function (Blueprint $table) {
            $table->dropConstrainedForeignId('broker_id');
        });

        Schema::table('broker_transactions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('property_deal_id');
        });
    }
};
