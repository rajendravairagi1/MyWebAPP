<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('unit_payments', function (Blueprint $table) {
            $table->string('purpose')->default('installment')->after('amount');
            $table->text('description')->nullable()->after('purpose');
        });
    }

    public function down(): void
    {
        Schema::table('unit_payments', function (Blueprint $table) {
            $table->dropColumn(['purpose', 'description']);
        });
    }
};
