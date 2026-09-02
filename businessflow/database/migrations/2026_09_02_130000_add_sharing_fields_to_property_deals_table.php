<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('property_deals', function (Blueprint $table) {
            $table->string('share_token')->nullable()->unique()->after('status');
            $table->decimal('asking_price', 15, 2)->nullable()->after('purchase_price');
            $table->string('contact_name')->nullable()->after('notes');
            $table->string('contact_phone', 30)->nullable()->after('contact_name');
            $table->string('contact_email')->nullable()->after('contact_phone');
        });
    }

    public function down(): void
    {
        Schema::table('property_deals', function (Blueprint $table) {
            $table->dropColumn(['share_token', 'asking_price', 'contact_name', 'contact_phone', 'contact_email']);
        });
    }
};
