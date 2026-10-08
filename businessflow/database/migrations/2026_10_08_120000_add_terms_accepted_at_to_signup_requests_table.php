<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Timestamped proof that a signup explicitly accepted the Terms of
 * Service / Privacy Policy, kept on the request itself rather than
 * inferred from the account later — see
 * Public\SignupRequestController::store().
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('signup_requests', function (Blueprint $table) {
            $table->timestamp('terms_accepted_at')->nullable()->after('address');
        });
    }

    public function down(): void
    {
        Schema::table('signup_requests', function (Blueprint $table) {
            $table->dropColumn('terms_accepted_at');
        });
    }
};
