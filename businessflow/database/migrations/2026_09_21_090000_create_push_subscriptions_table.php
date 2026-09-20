<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * A browser/device registration for Web Push (meeting, follow-up and
 * payment-reminder alerts landing on a phone as a real notification,
 * even when the app isn't open). Tied to the user, not a business —
 * the same signed-in phone should keep getting notified no matter
 * which of their businesses is active when the reminder fires.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('push_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('endpoint');
            $table->string('endpoint_hash', 64)->unique();
            $table->string('public_key');
            $table->string('auth_token');
            $table->string('content_encoding')->default('aesgcm');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('push_subscriptions');
    }
};
