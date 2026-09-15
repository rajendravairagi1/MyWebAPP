<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Tracks which "plan expires in N day(s)" value this user has
            // already opened the notification bell on — see
            // NotificationComposer::compose(). Only that exact day-count
            // still bumps the bell badge; it naturally reappears once the
            // countdown ticks over to a new day (or is renewed and clears).
            $table->unsignedTinyInteger('plan_expiry_seen_days')->nullable()->after('remember_token');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('plan_expiry_seen_days');
        });
    }
};
