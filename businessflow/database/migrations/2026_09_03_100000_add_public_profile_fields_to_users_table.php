<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('photo_path')->nullable()->after('avatar');
            $table->string('phone', 30)->nullable()->after('photo_path');
            $table->text('about')->nullable()->after('phone');
            $table->string('profile_token')->nullable()->unique()->after('about');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['photo_path', 'phone', 'about', 'profile_token']);
        });
    }
};
