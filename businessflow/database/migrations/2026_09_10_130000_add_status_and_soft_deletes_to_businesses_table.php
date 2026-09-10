<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            // The platform admin's own active/inactive switch for this
            // account (Platform Admin panel) — distinct from business_user's
            // per-team-member status column. An inactive account is blocked
            // from access the same way an expired subscription is (see
            // EnsureSubscriptionActive).
            $table->string('status')->default('active');

            // "Remove" in the admin panel archives the account (soft
            // delete) instead of destroying it — nothing here is ever
            // permanently deleted, so a removed account can always be
            // restored later.
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropSoftDeletes();
        });
    }
};
