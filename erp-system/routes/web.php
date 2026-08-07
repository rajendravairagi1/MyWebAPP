<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RawMaterialController;
use App\Http\Controllers\RoleManagementController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware('role:Owner')->group(function () {
        Route::get('/roles', [RoleManagementController::class, 'index'])->name('roles.index');
        Route::get('/roles/{role}', [RoleManagementController::class, 'edit'])->name('roles.edit');
        Route::put('/roles/{role}', [RoleManagementController::class, 'update'])->name('roles.update');
    });

    Route::middleware('permission:module.crm-customers')->group(function () {
        Route::resource('customers', CustomerController::class);
        Route::resource('products', ProductController::class)->except(['show']);
    });

    Route::middleware('permission:module.hr-employees')->group(function () {
        Route::resource('employees', EmployeeController::class)->except(['show']);
    });

    Route::middleware('permission:module.store-material')->group(function () {
        Route::resource('raw-materials', RawMaterialController::class)->except(['show']);
    });

    Route::middleware('permission:module.job-work')->group(function () {
        Route::get('/jobs', [JobController::class, 'index'])->name('jobs.index');
        Route::get('/jobs/create', [JobController::class, 'create'])->name('jobs.create');
        Route::post('/jobs', [JobController::class, 'store'])->name('jobs.store');
    });

    Route::middleware('permission:module.job-work|module.quality|module.store-material|module.invoice-accounts|module.security-gate')->group(function () {
        Route::get('/jobs/{job}', [JobController::class, 'show'])->name('jobs.show');
    });

    Route::middleware('permission:module.job-work|module.quality')->group(function () {
        Route::post('/jobs/{job}/advance-stage', [JobController::class, 'advanceStage'])->name('jobs.advance-stage');
    });

    Route::middleware('permission:module.store-material')->group(function () {
        Route::post('/jobs/{job}/issue-material', [JobController::class, 'issueMaterial'])->name('jobs.issue-material');
    });

    Route::middleware('permission:module.security-gate')->group(function () {
        Route::post('/jobs/{job}/verify-gate', [JobController::class, 'verifyGate'])->name('jobs.verify-gate');
    });

    Route::middleware('permission:module.invoice-accounts')->group(function () {
        Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('/jobs/{job}/invoice/create', [InvoiceController::class, 'create'])->name('invoices.create');
        Route::post('/jobs/{job}/invoice', [InvoiceController::class, 'store'])->name('invoices.store');
        Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
        Route::post('/invoices/{invoice}/payments', [InvoiceController::class, 'addPayment'])->name('invoices.payments.store');
    });
});

require __DIR__.'/auth.php';
require __DIR__.'/migrate.php';
