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
            //
            // Guarded with hasColumn() so a deploy on a database where one
            // of these two columns already landed (e.g. an install that
            // only partially applied this migration before) doesn't abort
            // the whole statement and leave the other column missing too.
            if (! Schema::hasColumn('businesses', 'status')) {
                $table->string('status')->default('active');
            }

            // "Remove" in the admin panel archives the account (soft
            // delete) instead of destroying it — nothing here is ever
            // permanently deleted, so a removed account can always be
            // restored later.
            if (! Schema::hasColumn('businesses', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            if (Schema::hasColumn('businesses', 'status')) {
                $table->dropColumn('status');
            }

            if (Schema::hasColumn('businesses', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};
