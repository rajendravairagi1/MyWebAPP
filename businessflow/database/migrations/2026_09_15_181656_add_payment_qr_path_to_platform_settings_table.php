<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('platform_settings', function (Blueprint $table) {
            // The platform owner's own UPI/bank QR code image, shown on
            // the billing/renew page every business sees to pay their
            // subscription - see BillingController and
            // Admin\AdminController::updateSettings().
            $table->string('payment_qr_path')->nullable()->after('support_whatsapp');
        });
    }

    public function down(): void
    {
        Schema::table('platform_settings', function (Blueprint $table) {
            $table->dropColumn('payment_qr_path');
        });
    }
};
