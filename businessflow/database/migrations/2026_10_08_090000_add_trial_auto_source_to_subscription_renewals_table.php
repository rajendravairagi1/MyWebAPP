<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * A trial a signup starts themselves (see OnboardingController::store)
 * logs here with source 'trial_auto', alongside the existing
 * 'admin_manual' and 'google_play' — without this, the enum's own CHECK
 * constraint rejects the insert outright.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscription_renewals', function (Blueprint $table) {
            $table->enum('source', ['admin_manual', 'google_play', 'trial_auto'])->change();
        });
    }

    public function down(): void
    {
        Schema::table('subscription_renewals', function (Blueprint $table) {
            $table->enum('source', ['admin_manual', 'google_play'])->change();
        });
    }
};
