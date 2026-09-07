<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('followups', function (Blueprint $table) {
            $table->foreignId('lead_id')->nullable()->after('customer_id')->constrained()->cascadeOnDelete();
        });

        // A follow-up already had to belong to a customer; it can now
        // belong to a lead instead, so the column can no longer be
        // required. SQLite can't alter a column's nullability in place,
        // so rebuild the table the way Laravel's schema builder does for
        // any other "change" on this driver.
        Schema::table('followups', function (Blueprint $table) {
            $table->foreignId('customer_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('followups', function (Blueprint $table) {
            $table->dropForeign(['lead_id']);
            $table->dropColumn('lead_id');
            $table->foreignId('customer_id')->nullable(false)->change();
        });
    }
};
