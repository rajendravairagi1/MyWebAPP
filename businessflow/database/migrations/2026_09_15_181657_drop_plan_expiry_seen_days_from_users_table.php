<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Superseded before it ever shipped a working fix: per-user "seen"
 * tracking for the plan-expiry bell notice turned out to be the wrong
 * approach entirely - the notice should never count toward the bell
 * badge at all (see NotificationComposer / layouts.app), not just stop
 * counting once opened. Drops the now-unused column from the previous
 * migration.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'plan_expiry_seen_days')) {
                $table->dropColumn('plan_expiry_seen_days');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedTinyInteger('plan_expiry_seen_days')->nullable()->after('remember_token');
        });
    }
};
