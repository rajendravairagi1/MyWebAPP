<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('login_logs', function (Blueprint $table) {
            $table->id();
            // nullOnDelete + a name/email snapshot: a login record should
            // outlive the user account it belonged to, for security review.
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('user_name');
            $table->string('user_email');
            $table->string('business_name')->nullable();
            $table->string('ip_address', 45);
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('device_type', 20); // mobile | tablet | desktop
            $table->string('platform', 30); // Windows, macOS, Android, iOS, ...
            $table->string('browser', 30);
            $table->text('user_agent')->nullable();
            $table->timestamp('logged_in_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('login_logs');
    }
};
