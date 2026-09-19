<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The builder's own construction financing for a project — distinct
 * from App\Models\Loan, which is a customer's home loan against one
 * unit they're buying. Here the money flows the other way: the builder
 * borrows it to fund the project, and owes principal + interest back
 * to the lender over time.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('lender_name');
            $table->string('account_number')->nullable();
            $table->decimal('principal_amount', 14, 2);
            $table->decimal('interest_rate', 5, 2)->default(0); // annual %
            $table->string('repayment_type')->default('emi'); // emi | full_payment
            $table->unsignedInteger('tenure_months')->nullable();
            $table->date('disbursed_at');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['project_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_loans');
    }
};
