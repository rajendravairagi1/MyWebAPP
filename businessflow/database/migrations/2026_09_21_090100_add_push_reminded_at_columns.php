<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Marks when a push reminder was last sent for this record, so the
 * due-reminder sweep (App\Console\Commands\SendDueNotifications) never
 * notifies the same meeting/follow-up/invoice twice.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('meetings', function (Blueprint $table) {
            $table->timestamp('push_reminded_at')->nullable()->after('status');
        });

        Schema::table('followups', function (Blueprint $table) {
            $table->timestamp('push_reminded_at')->nullable()->after('status');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->timestamp('push_reminded_at')->nullable()->after('due_date');
        });
    }

    public function down(): void
    {
        Schema::table('meetings', function (Blueprint $table) {
            $table->dropColumn('push_reminded_at');
        });

        Schema::table('followups', function (Blueprint $table) {
            $table->dropColumn('push_reminded_at');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('push_reminded_at');
        });
    }
};
