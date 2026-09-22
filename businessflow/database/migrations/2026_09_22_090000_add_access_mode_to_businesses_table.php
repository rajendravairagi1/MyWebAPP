<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Lets a business be restricted to mobile devices only — see
 * App\Http\Middleware\EnsureAccessChannel. Every existing business
 * defaults to 'all_devices' (no behavior change on deploy).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->string('access_mode')->default('all_devices')->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn('access_mode');
        });
    }
};
