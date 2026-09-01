<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_costs', function (Blueprint $table) {
            $table->boolean('is_credit')->default(false)->after('payment_account_id');
            $table->date('credit_settled_at')->nullable()->after('is_credit');
        });
    }

    public function down(): void
    {
        Schema::table('project_costs', function (Blueprint $table) {
            $table->dropColumn(['is_credit', 'credit_settled_at']);
        });
    }
};
