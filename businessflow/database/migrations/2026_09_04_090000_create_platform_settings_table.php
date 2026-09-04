<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * A single-row table (see App\Models\PlatformSetting::current()) for
 * the handful of values the platform owner controls across every
 * business — the footer credit line and the support WhatsApp number —
 * rather than a customer's own Business Settings, since these apply to
 * every tenant on the install, not to any one business.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('platform_settings', function (Blueprint $table) {
            $table->id();
            $table->string('footer_text')->nullable();
            $table->string('support_whatsapp')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('platform_settings');
    }
};
