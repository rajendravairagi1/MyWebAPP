<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            // Cleared automatically whenever subscription_expires_at changes
            // (see Business::update() / AdminController) — so dismissing
            // this cycle's nudge never silently hides a *future* renewal.
            $table->timestamp('renewal_alert_dismissed_at')->nullable()->after('subscription_expires_at');
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->timestamp('renewal_alert_dismissed_at')->nullable()->after('subscription_expires_at');
        });
    }

    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn('renewal_alert_dismissed_at');
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('renewal_alert_dismissed_at');
        });
    }
};
