<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Backs the notification bell (see NotificationComposer + AppNotification
 * model) — one row per lead/follow-up/commitment/meeting the bell has
 * ever surfaced for a business, so "Done" (dismissed_at) can hide it
 * from the bell while the full history stays browsable on /notifications
 * until someone actually deletes it. business_id + type + source_id is
 * unique so re-computing "what's due" on every page load never creates
 * duplicate rows for the same underlying lead/follow-up/etc.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('app_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('type', 30);
            $table->unsignedBigInteger('source_id');
            $table->string('title');
            $table->string('body')->nullable();
            $table->string('url')->nullable();
            $table->timestamp('dismissed_at')->nullable();
            $table->timestamps();

            $table->unique(['business_id', 'type', 'source_id']);
            $table->index(['business_id', 'dismissed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_notifications');
    }
};
