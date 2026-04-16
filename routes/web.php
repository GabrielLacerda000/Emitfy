<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\InvoicePdfController;
use App\Http\Controllers\PublicInvoiceController;
use App\Http\Controllers\Subscription\PixCheckoutController;
use App\Models\Plan;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
        'planId'      => Plan::first()?->id,
    ]);
})->name('home');

Route::get('/i/{public_token}', [PublicInvoiceController::class, 'show'])
    ->name('invoices.public');

Route::get('dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('checkout/pix', [PixCheckoutController::class, 'store'])->name('checkout.pix.store');
    Route::get('checkout/pix/{payment}', [PixCheckoutController::class, 'show'])->name('checkout.pix.show');

    Route::resource('clients', ClientController::class);
    Route::get('invoices/export', [ExportController::class, 'invoicesCsv'])->name('invoices.export');
    Route::resource('invoices', InvoiceController::class);
    Route::post('invoices/{invoice}/send', [InvoiceController::class, 'send'])->name('invoices.send');
    Route::get('invoices/{invoice}/pdf', [InvoicePdfController::class, 'show'])->name('invoices.pdf');
});

require __DIR__.'/settings.php';
