<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\ClientDashboardController;
use App\Http\Middleware\IsAdmin;
use App\Http\Controllers\Admin\ClientController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('dashboard');
});

// breeze auth
require __DIR__.'/auth.php';

Route::middleware(['auth'])->group(function () {

    // Redirect generic after login
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('client.dashboard');
    })->name('dashboard');

    // Client (any authenticated)
    Route::get('/client', [ClientDashboardController::class, 'index'])->name('client.dashboard');

    // Admin only
    Route::prefix('admin')
    ->middleware(['auth', IsAdmin::class])
    ->group(function () {

        Route::get('/', [App\Http\Controllers\AdminDashboardController::class, 'index'])
            ->name('admin.dashboard');

        // semua route admin lain taruh di sini
        Route::resource('clients', App\Http\Controllers\Admin\ClientController::class)
            ->names('admin.clients');
        Route::resource('services', App\Http\Controllers\Admin\ServiceController::class)
    ->names('admin.services');
    Route::resource('client', App\Http\Controllers\Admin\ClientController::class)
    ->names('admin.client');
    Route::resource('invoices', App\Http\Controllers\Admin\InvoiceController::class)
    ->names('admin.invoices');
    Route::post('invoices/{invoice}/pay', [App\Http\Controllers\Admin\InvoiceController::class, 'pay'])->name('admin.invoices.pay');
    ///akhir darigrp admin
    });
    // trigger create invoice and redirect customer to payment page
    Route::post('admin/invoices/{invoice}/pay', [App\Http\Controllers\Admin\InvoiceController::class, 'pay'])->name('admin.invoices.pay');
    Route::post('/payment/callback', [App\Http\Controllers\Payment\DuitkuController::class, 'callback'])->name('duitku.callback');
    Route::get('/payment/return', [App\Http\Controllers\Payment\DuitkuController::class, 'return'])->name('duitku.return');
    
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
});
