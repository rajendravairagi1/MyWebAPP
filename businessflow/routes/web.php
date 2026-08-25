<?php

use App\Http\Controllers\CompletedProjectsController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerDocumentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FollowupController;
use App\Http\Controllers\InstallController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LedgerController;
use App\Http\Controllers\MigrateController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectCostController;
use App\Http\Controllers\ProjectUnitController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\ResetDataController;
use App\Http\Controllers\UnitPaymentController;
use App\Http\Controllers\VerifyController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/install', [InstallController::class, 'index'])->name('install.index');
Route::post('/install', [InstallController::class, 'store'])->name('install.store');
Route::get('/migrate', MigrateController::class)->name('migrate');

// Public, signed verification pages linked from the QR code printed on
// Quotation/Invoice/Statement PDFs — confirms a document is genuine
// without requiring the viewer to have an account.
Route::get('/verify/quotation/{quotation}', [VerifyController::class, 'quotation'])->name('verify.quotation')->middleware('signed');
Route::get('/verify/invoice/{invoice}', [VerifyController::class, 'invoice'])->name('verify.invoice')->middleware('signed');
Route::get('/verify/customer/{customer}', [VerifyController::class, 'customer'])->name('verify.customer')->middleware('signed');

Route::middleware('auth')->group(function () {
    Route::get('/onboarding/business', [OnboardingController::class, 'create'])->name('onboarding.create');
    Route::post('/onboarding/business', [OnboardingController::class, 'store'])->name('onboarding.store');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/reset-data', [ResetDataController::class, 'index'])->name('reset-data.index');
    Route::post('/reset-data', [ResetDataController::class, 'store'])->name('reset-data.store');

    Route::resource('customers', CustomerController::class);
    Route::get('/customers/{customer}/statement', [CustomerController::class, 'statement'])->name('customers.statement');
    Route::get('/customers/{customer}/photo', [CustomerController::class, 'photo'])->name('customers.photo');
    Route::get('/customers/{customer}/aadhar', [CustomerController::class, 'aadhar'])->name('customers.aadhar');
    Route::post('/customers/{customer}/documents', [CustomerDocumentController::class, 'store'])->name('customer-documents.store');
    Route::get('/customers/{customer}/documents/{document}/download', [CustomerDocumentController::class, 'download'])->name('customer-documents.download');
    Route::delete('/customers/{customer}/documents/{document}', [CustomerDocumentController::class, 'destroy'])->name('customer-documents.destroy');
    Route::resource('products', ProductController::class)->except(['show']);

    Route::resource('projects', ProjectController::class)->except(['destroy']);
    Route::post('/projects/{project}/costs', [ProjectCostController::class, 'store'])->name('project-costs.store');
    Route::put('/projects/{project}/costs/{cost}', [ProjectCostController::class, 'update'])->name('project-costs.update');
    Route::delete('/projects/{project}/costs/{cost}', [ProjectCostController::class, 'destroy'])->name('project-costs.destroy');
    Route::get('/projects/{project}/costs/{cost}/bill', [ProjectCostController::class, 'bill'])->name('project-costs.bill');
    Route::post('/projects/{project}/units', [ProjectUnitController::class, 'store'])->name('project-units.store');
    Route::put('/projects/{project}/units/{unit}', [ProjectUnitController::class, 'update'])->name('project-units.update');
    Route::delete('/projects/{project}/units/{unit}', [ProjectUnitController::class, 'destroy'])->name('project-units.destroy');
    Route::post('/project-units/assign', [ProjectUnitController::class, 'assign'])->name('project-units.assign');
    Route::post('/project-units/{unit}/write-off', [ProjectUnitController::class, 'writeOff'])->name('project-units.write-off');
    Route::post('/project-units/{unit}/recover', [ProjectUnitController::class, 'recover'])->name('project-units.recover');
    Route::post('/project-units/{unit}/commitment', [ProjectUnitController::class, 'updateCommitment'])->name('project-units.commitment');
    Route::post('/project-units/{unit}/payments', [UnitPaymentController::class, 'store'])->name('unit-payments.store');
    Route::put('/project-units/{unit}/payments/{payment}', [UnitPaymentController::class, 'update'])->name('unit-payments.update');
    Route::delete('/project-units/{unit}/payments/{payment}', [UnitPaymentController::class, 'destroy'])->name('unit-payments.destroy');

    Route::get('/followups', [FollowupController::class, 'index'])->name('followups.index');
    Route::get('/followups/create', [FollowupController::class, 'create'])->name('followups.create');
    Route::post('/followups', [FollowupController::class, 'store'])->name('followups.store');
    Route::post('/followups/{followup}/complete', [FollowupController::class, 'complete'])->name('followups.complete');
    Route::delete('/followups/{followup}', [FollowupController::class, 'destroy'])->name('followups.destroy');

    Route::get('/quotations', [QuotationController::class, 'index'])->name('quotations.index');
    Route::get('/quotations/create', [QuotationController::class, 'create'])->name('quotations.create');
    Route::post('/quotations', [QuotationController::class, 'store'])->name('quotations.store');
    Route::get('/quotations/{quotation}', [QuotationController::class, 'show'])->name('quotations.show');
    Route::get('/quotations/{quotation}/edit', [QuotationController::class, 'edit'])->name('quotations.edit');
    Route::put('/quotations/{quotation}', [QuotationController::class, 'update'])->name('quotations.update');
    Route::post('/quotations/{quotation}/mark-sent', [QuotationController::class, 'markSent'])->name('quotations.mark-sent');
    Route::post('/quotations/{quotation}/convert', [QuotationController::class, 'convert'])->name('quotations.convert');
    Route::get('/quotations/{quotation}/pdf', [QuotationController::class, 'pdf'])->name('quotations.pdf');

    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
    Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');
    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('/invoices/{invoice}/edit', [InvoiceController::class, 'edit'])->name('invoices.edit');
    Route::put('/invoices/{invoice}', [InvoiceController::class, 'update'])->name('invoices.update');
    Route::post('/invoices/{invoice}/mark-sent', [InvoiceController::class, 'markSent'])->name('invoices.mark-sent');
    Route::get('/invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('invoices.pdf');

    Route::post('/invoices/{invoice}/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::delete('/invoices/{invoice}/payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');

    Route::get('/ledger', [LedgerController::class, 'index'])->name('ledger.index');
    Route::post('/ledger/entries', [LedgerController::class, 'storeEntry'])->name('ledger.entries.store');
    Route::delete('/ledger/entries/{entry}', [LedgerController::class, 'destroyEntry'])->name('ledger.entries.destroy');

    Route::get('/completed-projects', [CompletedProjectsController::class, 'index'])->name('completed-projects.index');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
