<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('brokers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('broker_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('broker_id')->constrained()->cascadeOnDelete();
            // Which sale earned this commission — optional since a broker
            // payment or a manually-entered commission isn't always tied
            // to one specific unit.
            $table->foreignId('project_unit_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type'); // commission_accrued | payment_paid
            $table->decimal('amount', 14, 2);
            // Set only when the commission was calculated as a % of the
            // unit's price rather than typed in as a flat figure — kept
            // for reference, the amount column is always the source of
            // truth for totals.
            $table->decimal('commission_percent', 5, 2)->nullable();
            $table->date('transaction_date');
            $table->string('method')->nullable();
            $table->string('reference')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('broker_transactions');
        Schema::dropIfExists('brokers');
    }
};
