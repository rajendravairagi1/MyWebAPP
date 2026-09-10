<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\BlogController as AdminBlogController;
use App\Http\Controllers\Admin\BrandingController as AdminBrandingController;
use App\Http\Controllers\Admin\FaqsController as AdminFaqsController;
use App\Http\Controllers\Admin\IntegrationsController as AdminIntegrationsController;
use App\Http\Controllers\Admin\LeadsController as AdminLeadsController;
use App\Http\Controllers\Admin\MaintenanceController as AdminMaintenanceController;
use App\Http\Controllers\Admin\PricingController as AdminPricingController;
use App\Http\Controllers\Admin\SecurityController as AdminSecurityController;
use App\Http\Controllers\Admin\SocialController as AdminSocialController;
use App\Http\Controllers\Admin\TestimonialsController as AdminTestimonialsController;
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
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store')->middleware('throttle:5,1');

Route::get('/migrate', MigrateController::class)->name('migrate');

// Stable logo URL other apps (businessflow) hardcode, independent of the
// uploaded file's actual extension - see BrandingController::showLogo().
Route::get('/branding/logo', [AdminBrandingController::class, 'showLogo'])->name('branding.logo');
Route::get('/branding/logo-size', [AdminBrandingController::class, 'logoSize'])->name('branding.logo-size');

Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('robots');
Route::get('/llms.txt', [SeoController::class, 'llms'])->name('llms');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit')->middleware('throttle:5,1');
    Route::get('/login/verify', [AdminAuthController::class, 'showVerify'])->name('login.verify');
    Route::post('/login/verify', [AdminAuthController::class, 'verify'])->name('login.verify.submit')->middleware('throttle:10,1');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    Route::middleware('admin.auth')->group(function () {
        Route::redirect('/', '/admin/posts')->name('dashboard');

        Route::get('/security', [AdminSecurityController::class, 'index'])->name('security.index');
        Route::post('/security/start', [AdminSecurityController::class, 'start'])->name('security.start');
        Route::post('/security/confirm', [AdminSecurityController::class, 'confirm'])->name('security.confirm');
        Route::post('/security/cancel', [AdminSecurityController::class, 'cancel'])->name('security.cancel');
        Route::post('/security/disable', [AdminSecurityController::class, 'disable'])->name('security.disable');
        Route::post('/security/backup-codes', [AdminSecurityController::class, 'regenerateBackupCodes'])->name('security.backup-codes');

        Route::get('/posts', [AdminBlogController::class, 'index'])->name('posts.index');
        Route::get('/posts/create', [AdminBlogController::class, 'create'])->name('posts.create');
        Route::post('/posts', [AdminBlogController::class, 'store'])->name('posts.store');
        Route::get('/posts/{post}/edit', [AdminBlogController::class, 'edit'])->name('posts.edit');
        Route::put('/posts/{post}', [AdminBlogController::class, 'update'])->name('posts.update');
        Route::delete('/posts/{post}', [AdminBlogController::class, 'destroy'])->name('posts.destroy');

        Route::get('/pricing', [AdminPricingController::class, 'index'])->name('pricing.index');
        Route::put('/pricing-discount', [AdminPricingController::class, 'updateDiscount'])->name('pricing.discount');
        Route::get('/pricing/{plan}/edit', [AdminPricingController::class, 'edit'])->name('pricing.edit');
        Route::put('/pricing/{plan}', [AdminPricingController::class, 'update'])->name('pricing.update');

        Route::get('/theme', [AdminThemeController::class, 'index'])->name('theme.index');
        Route::put('/theme', [AdminThemeController::class, 'update'])->name('theme.update');

        Route::get('/branding', [AdminBrandingController::class, 'index'])->name('branding.index');
        Route::post('/branding', [AdminBrandingController::class, 'update'])->name('branding.update');
        Route::delete('/branding', [AdminBrandingController::class, 'destroy'])->name('branding.destroy');
        Route::put('/branding/size', [AdminBrandingController::class, 'updateSize'])->name('branding.size');
        Route::post('/branding/favicon', [AdminBrandingController::class, 'updateFavicon'])->name('branding.favicon.update');
        Route::delete('/branding/favicon', [AdminBrandingController::class, 'destroyFavicon'])->name('branding.favicon.destroy');

        Route::get('/integrations', [AdminIntegrationsController::class, 'index'])->name('integrations.index');
        Route::put('/integrations', [AdminIntegrationsController::class, 'update'])->name('integrations.update');
        Route::post('/integrations/ping-sitemap', [AdminIntegrationsController::class, 'pingSitemap'])->name('integrations.ping-sitemap');

        Route::get('/leads', [AdminLeadsController::class, 'index'])->name('leads.index');
        Route::delete('/leads/{lead}', [AdminLeadsController::class, 'destroy'])->name('leads.destroy');

        Route::get('/testimonials', [AdminTestimonialsController::class, 'index'])->name('testimonials.index');
        Route::get('/testimonials/create', [AdminTestimonialsController::class, 'create'])->name('testimonials.create');
        Route::post('/testimonials', [AdminTestimonialsController::class, 'store'])->name('testimonials.store');
        Route::get('/testimonials/{testimonial}/edit', [AdminTestimonialsController::class, 'edit'])->name('testimonials.edit');
        Route::put('/testimonials/{testimonial}', [AdminTestimonialsController::class, 'update'])->name('testimonials.update');
        Route::delete('/testimonials/{testimonial}', [AdminTestimonialsController::class, 'destroy'])->name('testimonials.destroy');

        Route::get('/faqs', [AdminFaqsController::class, 'index'])->name('faqs.index');
        Route::get('/faqs/create', [AdminFaqsController::class, 'create'])->name('faqs.create');
        Route::post('/faqs', [AdminFaqsController::class, 'store'])->name('faqs.store');
        Route::get('/faqs/{faq}/edit', [AdminFaqsController::class, 'edit'])->name('faqs.edit');
        Route::put('/faqs/{faq}', [AdminFaqsController::class, 'update'])->name('faqs.update');
        Route::delete('/faqs/{faq}', [AdminFaqsController::class, 'destroy'])->name('faqs.destroy');

        Route::get('/social', [AdminSocialController::class, 'index'])->name('social.index');
        Route::put('/social', [AdminSocialController::class, 'update'])->name('social.update');

        Route::get('/maintenance', [AdminMaintenanceController::class, 'index'])->name('maintenance.index');
        Route::post('/maintenance/clear-cache', [AdminMaintenanceController::class, 'clearCache'])->name('maintenance.clear-cache');
        Route::post('/maintenance/migrate', [AdminMaintenanceController::class, 'runMigrations'])->name('maintenance.migrate');
        Route::get('/maintenance/backup/database', [AdminMaintenanceController::class, 'downloadDatabase'])->name('maintenance.backup.database');
        Route::get('/maintenance/backup/full', [AdminMaintenanceController::class, 'downloadFullBackup'])->name('maintenance.backup.full');
    });
});
