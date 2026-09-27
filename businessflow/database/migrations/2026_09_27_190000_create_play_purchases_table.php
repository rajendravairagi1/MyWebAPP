<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * One row per Google Play subscription a Business has ever held — kept
 * even after it lapses/cancels, both as an audit trail and because
 * Real-time Developer Notifications (see RunGooglePlayNotificationWebhook)
 * arrive keyed by purchase_token, not by business, and need somewhere to
 * look the business up from.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('play_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();

            // The Play Console "Product ID" for the subscription base
            // plan the business bought — see PlayBillingService::PRODUCT_TO_PLAN
            // for how this maps back to Business::plan ('solo'/'team'/'company').
            $table->string('product_id');

            // Unique per purchase — Google's own identifier for this
            // specific subscription instance, and how RTDN tells us which
            // row a webhook event is about.
            $table->string('purchase_token')->unique();

            // Google's own order id (e.g. "GPA.1234-5678-9012-34567"),
            // null until the first successful charge comes through.
            $table->string('order_id')->nullable();

            // Mirrors the Google Play Developer API's own subscription
            // states (subscriptionState in the v2 API) — not a smaller,
            // re-interpreted set of our own, so a webhook payload maps
            // onto this column with no translation layer to keep in sync.
            $table->enum('status', [
                'active', 'canceled', 'in_grace_period', 'on_hold', 'paused', 'expired', 'revoked',
            ])->default('active');

            // When the current paid period actually runs out — this (not
            // "status") is what Business::subscription_expires_at gets
            // set from, so a canceled-but-not-yet-expired subscription
            // still keeps access until this date, same as the manual
            // UPI flow already works.
            $table->timestamp('expiry_time');

            // Google auto-refunds an unacknowledged purchase after 3
            // days — this is set the moment PlayBillingService
            // successfully calls the acknowledge endpoint, so a retry
            // never double-acknowledges the same token.
            $table->timestamp('acknowledged_at')->nullable();

            // The full decoded API response from the last time this
            // purchase was looked up — not read by the app itself, only
            // ever opened by hand while debugging a specific purchase.
            $table->json('last_api_response')->nullable();

            $table->timestamps();

            $table->index(['business_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('play_purchases');
    }
};
