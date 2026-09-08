<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            if (! Schema::hasColumn('blog_posts', 'featured_image_alt')) {
                $table->string('featured_image_alt')->nullable()->after('featured_image');
            }
            if (! Schema::hasColumn('blog_posts', 'featured_image_caption')) {
                $table->string('featured_image_caption')->nullable()->after('featured_image_alt');
            }
            if (! Schema::hasColumn('blog_posts', 'featured_image_size')) {
                $table->string('featured_image_size')->default('lg')->after('featured_image_caption');
            }
        });
    }

    public function down(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->dropColumn(['featured_image_alt', 'featured_image_caption', 'featured_image_size']);
        });
    }
};
