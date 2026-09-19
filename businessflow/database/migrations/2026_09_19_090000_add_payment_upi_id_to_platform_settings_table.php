<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('platform_settings', function (Blueprint $table) {
            // Replaces the static uploaded QR image as the preferred
            // source: a QR generated fresh from this UPI ID on every page
            // load can never fall out of sync with it, unlike an image
            // file someone has to remember to re-upload after changing
            // banks. payment_qr_path is kept as a fallback for an
            // install that set one before this existed.
            if (! Schema::hasColumn('platform_settings', 'payment_upi_id')) {
                $table->string('payment_upi_id')->nullable()->after('payment_qr_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('platform_settings', function (Blueprint $table) {
            if (Schema::hasColumn('platform_settings', 'payment_upi_id')) {
                $table->dropColumn('payment_upi_id');
            }
        });
    }
};
