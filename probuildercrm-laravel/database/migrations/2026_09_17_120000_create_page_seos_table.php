<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_seos', function (Blueprint $table) {
            $table->id();
            // Matches the route name of the page it controls (e.g. 'home',
            // 'features', 'blog.index') - see App\Models\PageSeo::PAGES.
            $table->string('page_key')->unique();
            $table->string('meta_title')->nullable();
            $table->string('meta_description', 500)->nullable();
            $table->text('meta_keywords')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_seos');
    }
};
