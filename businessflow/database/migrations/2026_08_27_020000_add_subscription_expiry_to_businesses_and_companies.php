<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            // Null means no expiry (lifetime/comped) — payment is collected
            // manually outside the app, so this is the platform admin's own
            // record of when to chase renewal, enforced by IdentifyTenant.
            $table->date('subscription_expires_at')->nullable()->after('plan');
        });

        Schema::table('companies', function (Blueprint $table) {
            // A builder under a branch has no billing of its own — its
            // company pays once for every branch/builder underneath, so
            // expiry lives here rather than on each business row.
            $table->date('subscription_expires_at')->nullable()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn('subscription_expires_at');
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('subscription_expires_at');
        });
    }
};
