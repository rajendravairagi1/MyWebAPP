<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('photo_path')->nullable()->after('tags');
            $table->string('aadhar_path')->nullable()->after('photo_path');
            $table->string('aadhar_name')->nullable()->after('aadhar_path');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['photo_path', 'aadhar_path', 'aadhar_name']);
        });
    }
};
