<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_loan_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_loan_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 14, 2);
            // How this one payment split between interest accrued since
            // the previous payment (or since disbursement, for the
            // first) and principal — see App\Models\ProjectLoan::
            // recalculateSplits(), which recomputes these for every
            // payment on the loan whenever one is added, edited or
            // removed, so an out-of-order or backdated entry never
            // leaves a stale split on the payments after it.
            $table->decimal('interest_portion', 14, 2)->default(0);
            $table->decimal('principal_portion', 14, 2)->default(0);
            $table->date('paid_at');
            $table->string('method')->nullable();
            $table->string('reference')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['project_loan_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_loan_payments');
    }
};
