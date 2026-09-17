<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            // All nullable and optional - blank means "fall back to the
            // post's own title/excerpt", so existing posts need no edits.
            if (! Schema::hasColumn('blog_posts', 'meta_title')) {
                $table->string('meta_title')->nullable()->after('title');
            }
            if (! Schema::hasColumn('blog_posts', 'meta_description')) {
                $table->string('meta_description', 500)->nullable()->after('excerpt');
            }
            if (! Schema::hasColumn('blog_posts', 'meta_keywords')) {
                $table->text('meta_keywords')->nullable()->after('meta_description');
            }
        });
    }

    public function down(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            if (Schema::hasColumn('blog_posts', 'meta_title')) {
                $table->dropColumn('meta_title');
            }
            if (Schema::hasColumn('blog_posts', 'meta_description')) {
                $table->dropColumn('meta_description');
            }
            if (Schema::hasColumn('blog_posts', 'meta_keywords')) {
                $table->dropColumn('meta_keywords');
            }
        });
    }
};
