<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // A builder rarely receives/pays everything through one account —
        // money moves through a spouse's, a parent's, or a partner's
        // account too. This is that list of named "where did it actually
        // go/come from" accounts, so it can be reconstructed later (e.g.
        // for ITR) which account handled which payment.
        Schema::create('payment_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::table('unit_payments', function (Blueprint $table) {
            $table->foreignId('payment_account_id')->nullable()->after('method')->constrained()->nullOnDelete();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('payment_account_id')->nullable()->after('method')->constrained()->nullOnDelete();
        });

        Schema::table('ledger_entries', function (Blueprint $table) {
            $table->foreignId('payment_account_id')->nullable()->after('amount')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('unit_payments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('payment_account_id');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('payment_account_id');
        });

        Schema::table('ledger_entries', function (Blueprint $table) {
            $table->dropConstrainedForeignId('payment_account_id');
        });

        Schema::dropIfExists('payment_accounts');
    }
};
