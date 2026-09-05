<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->boolean('smart_alerts_enabled')->default(true)->after('is_demo');
            $table->boolean('payment_reminders_enabled')->default(true)->after('smart_alerts_enabled');
            $table->boolean('voice_notes_enabled')->default(true)->after('payment_reminders_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn(['smart_alerts_enabled', 'payment_reminders_enabled', 'voice_notes_enabled']);
        });
    }
};
