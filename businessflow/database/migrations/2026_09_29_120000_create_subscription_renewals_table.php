<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_renewals', function (Blueprint $table) {
            $table->id();
            // Exactly one of these is set — a solo/team business bills
            // itself directly, a company plan bills at the Company level
            // (see App\Models\Company) and its branches have no billing
            // of their own.
            $table->foreignId('business_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->nullable()->constrained()->cascadeOnDelete();
            $table->enum('source', ['admin_manual', 'google_play']);
            $table->string('plan')->nullable();
            $table->date('previous_expires_at')->nullable();
            $table->date('new_expires_at')->nullable();
            $table->string('note')->nullable();
            $table->timestamps();

            $table->index(['business_id', 'created_at']);
            $table->index(['company_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_renewals');
    }
};
