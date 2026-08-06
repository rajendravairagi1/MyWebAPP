<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\EmployeeController;
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
});

require __DIR__.'/auth.php';
require __DIR__.'/setup.php';
require __DIR__.'/migrate.php';
