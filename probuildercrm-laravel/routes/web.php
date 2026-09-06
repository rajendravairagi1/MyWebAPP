<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\BlogController as AdminBlogController;
use App\Http\Controllers\Admin\PricingController as AdminPricingController;
use App\Http\Controllers\Admin\ThemeController as AdminThemeController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\MigrateController;
use App\Http\Controllers\PricingController;
use App\Http\Controllers\SeoController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/features', [PageController::class, 'features'])->name('features');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/faq', [PageController::class, 'faq'])->name('faq');
Route::get('/privacy-policy', [PageController::class, 'privacyPolicy'])->name('privacy-policy');
Route::get('/terms-of-service', [PageController::class, 'termsOfService'])->name('terms-of-service');

Route::get('/pricing', [PricingController::class, 'index'])->name('pricing');

Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

Route::get('/migrate', MigrateController::class)->name('migrate');

Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('robots');
Route::get('/llms.txt', [SeoController::class, 'llms'])->name('llms');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    Route::middleware('admin.auth')->group(function () {
        Route::redirect('/', '/admin/posts')->name('dashboard');

        Route::get('/posts', [AdminBlogController::class, 'index'])->name('posts.index');
        Route::get('/posts/create', [AdminBlogController::class, 'create'])->name('posts.create');
        Route::post('/posts', [AdminBlogController::class, 'store'])->name('posts.store');
        Route::get('/posts/{post}/edit', [AdminBlogController::class, 'edit'])->name('posts.edit');
        Route::put('/posts/{post}', [AdminBlogController::class, 'update'])->name('posts.update');
        Route::delete('/posts/{post}', [AdminBlogController::class, 'destroy'])->name('posts.destroy');

        Route::get('/pricing', [AdminPricingController::class, 'index'])->name('pricing.index');
        Route::get('/pricing/{plan}/edit', [AdminPricingController::class, 'edit'])->name('pricing.edit');
        Route::put('/pricing/{plan}', [AdminPricingController::class, 'update'])->name('pricing.update');

        Route::get('/theme', [AdminThemeController::class, 'index'])->name('theme.index');
        Route::put('/theme', [AdminThemeController::class, 'update'])->name('theme.update');
    });
});
