<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InstallController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuotationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/install', [InstallController::class, 'index'])->name('install.index');
Route::post('/install', [InstallController::class, 'store'])->name('install.store');

Route::middleware('auth')->group(function () {
    Route::get('/onboarding/business', [OnboardingController::class, 'create'])->name('onboarding.create');
    Route::post('/onboarding/business', [OnboardingController::class, 'store'])->name('onboarding.store');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('customers', CustomerController::class);
    Route::resource('products', ProductController::class)->except(['show']);

    Route::get('/quotations', [QuotationController::class, 'index'])->name('quotations.index');
    Route::get('/quotations/create', [QuotationController::class, 'create'])->name('quotations.create');
    Route::post('/quotations', [QuotationController::class, 'store'])->name('quotations.store');
    Route::get('/quotations/{quotation}', [QuotationController::class, 'show'])->name('quotations.show');
    Route::post('/quotations/{quotation}/mark-sent', [QuotationController::class, 'markSent'])->name('quotations.mark-sent');
    Route::post('/quotations/{quotation}/convert', [QuotationController::class, 'convert'])->name('quotations.convert');
    Route::get('/quotations/{quotation}/pdf', [QuotationController::class, 'pdf'])->name('quotations.pdf');

    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
    Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');
    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::post('/invoices/{invoice}/mark-sent', [InvoiceController::class, 'markSent'])->name('invoices.mark-sent');
    Route::get('/invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('invoices.pdf');

    Route::post('/invoices/{invoice}/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::delete('/invoices/{invoice}/payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
