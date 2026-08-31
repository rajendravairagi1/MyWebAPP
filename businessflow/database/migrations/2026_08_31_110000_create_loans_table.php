<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            // One loan per property purchase — a second loan on the same
            // unit (e.g. a top-up) is rare enough to handle as a note on
            // this one rather than a whole second record for v1.
            $table->foreignId('project_unit_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('bank_name');
            $table->string('loan_account_number')->nullable();
            $table->decimal('sanctioned_amount', 14, 2);
            $table->date('sanctioned_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Marks a unit_payments row as a bank disbursement (rather than a
        // direct customer payment) so it can be grouped and shown against
        // the loan it belongs to — the amount still counts toward the
        // property's normal Collected/Outstanding totals as-is, since a
        // disbursement is money received just like any other payment.
        Schema::table('unit_payments', function (Blueprint $table) {
            $table->foreignId('loan_id')->nullable()->after('customer_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('unit_payments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('loan_id');
        });

        Schema::dropIfExists('loans');
    }
};
