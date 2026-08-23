<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_units', function (Blueprint $table) {
            // Set once a unit's sale is fully collected or written off, so
            // it drops out of the customer's active property list and
            // shows up under "History" instead.
            $table->timestamp('archived_at')->nullable()->after('customer_id');
            $table->decimal('write_off_amount', 16, 2)->nullable()->after('archived_at');
            $table->text('write_off_note')->nullable()->after('write_off_amount');
            $table->timestamp('write_off_at')->nullable()->after('write_off_note');
        });
    }

    public function down(): void
    {
        Schema::table('project_units', function (Blueprint $table) {
            $table->dropColumn(['archived_at', 'write_off_amount', 'write_off_note', 'write_off_at']);
        });
    }
};
