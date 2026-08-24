<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_units', function (Blueprint $table) {
            // The possession/handover date promised to the customer for
            // this property, so we can be notified and check later whether
            // we delivered on time.
            $table->date('commitment_date')->nullable()->after('customer_id');
            $table->string('commitment_note')->nullable()->after('commitment_date');
        });
    }

    public function down(): void
    {
        Schema::table('project_units', function (Blueprint $table) {
            $table->dropColumn(['commitment_date', 'commitment_note']);
        });
    }
};
