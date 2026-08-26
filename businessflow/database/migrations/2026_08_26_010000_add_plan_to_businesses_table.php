<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            // solo | team | company — see App\Support\Tenant::planAllows().
            // A business inside a branch is always effectively 'company'
            // tier regardless of this column (see IdentifyTenant).
            $table->string('plan')->default('solo')->after('branch_id');
            $table->boolean('is_demo')->default(false)->after('plan');
        });

        // Grandfather in every business that already existed before plan
        // tiers were introduced — they were already using every feature
        // unrestricted, so this migration must not suddenly lock anyone
        // out of Team or anything else they were already using. Only
        // businesses created after this point (via the admin panel) get
        // a deliberately chosen plan.
        DB::table('businesses')->update(['plan' => 'company']);
    }

    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn(['plan', 'is_demo']);
        });
    }
};
