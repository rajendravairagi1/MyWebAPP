<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_submissions', function (Blueprint $table) {
            // Null = unread — powers the "new demo request" badge in the
            // admin sidebar. Set the moment the Demo Requests list is
            // opened (see Admin\LeadsController::index()), not on submit.
            $table->timestamp('read_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('contact_submissions', function (Blueprint $table) {
            $table->dropColumn('read_at');
        });
    }
};
