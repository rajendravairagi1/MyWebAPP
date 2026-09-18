<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            // Nullable and optional by design — its presence (not its
            // exact contents) is what turns the GST option on for every
            // invoice/quotation this business creates; see
            // resources/views/components/line-items.blade.php.
            if (! Schema::hasColumn('businesses', 'gst_number')) {
                $table->string('gst_number', 20)->nullable()->after('invoice_prefix');
            }
        });
    }

    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            if (Schema::hasColumn('businesses', 'gst_number')) {
                $table->dropColumn('gst_number');
            }
        });
    }
};
