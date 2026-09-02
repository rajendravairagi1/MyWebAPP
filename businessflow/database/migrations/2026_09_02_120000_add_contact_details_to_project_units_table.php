<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * A contact person for this specific property — who a customer should
 * actually call/email about it, set at listing time or any time after.
 * Deliberately separate from the business's own contact details (shown
 * alongside it on the share page/PDF) since the person handling a given
 * property is often not whoever's phone number is on the letterhead.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_units', function (Blueprint $table) {
            $table->string('contact_name')->nullable()->after('status');
            $table->string('contact_phone')->nullable()->after('contact_name');
            $table->string('contact_email')->nullable()->after('contact_phone');
        });
    }

    public function down(): void
    {
        Schema::table('project_units', function (Blueprint $table) {
            $table->dropColumn(['contact_name', 'contact_phone', 'contact_email']);
        });
    }
};
