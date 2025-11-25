<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\ClientDashboardController;
use App\Http\Middleware\IsAdmin;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\GoogleAuthController;

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

    // Client routes
    Route::get('/client', [ClientDashboardController::class, 'index'])->name('client.dashboard');
    
    // Client invoice routes
    Route::prefix('client')->name('client.')->group(function () {
        Route::get('/invoices', [App\Http\Controllers\InvoiceController::class, 'clientInvoices'])->name('invoices.index');
        Route::get('/invoices/{invoice}', [App\Http\Controllers\InvoiceController::class, 'clientShow'])->name('invoices.show');
        Route::get('/invoices/{invoice}/pdf', [App\Http\Controllers\InvoiceController::class, 'downloadPDF'])->name('invoices.pdf');
        Route::get('/invoices/{invoice}/pay', [App\Http\Controllers\PaymentController::class, 'show'])->name('invoices.pay');
        
        // Client service management routes
        Route::get('/services', [App\Http\Controllers\ServiceManagementController::class, 'index'])->name('services.index');
        Route::get('/services/{service}/manage', [App\Http\Controllers\ServiceManagementController::class, 'show'])->name('services.manage');
        Route::post('/services/{service}/update', [App\Http\Controllers\ServiceManagementController::class, 'update'])->name('services.update');
        Route::post('/services/{service}/support', [App\Http\Controllers\ServiceManagementController::class, 'contactSupport'])->name('services.support');
        
        // Client order routes
        Route::get('/orders/create', [App\Http\Controllers\Client\OrderController::class, 'create'])->name('orders.create');
        Route::post('/orders', [App\Http\Controllers\Client\OrderController::class, 'store'])->name('orders.store');
        Route::get('/orders/{invoice}/success', [App\Http\Controllers\Client\OrderController::class, 'success'])->name('orders.success');
        Route::get('/api/packages/{id}', [App\Http\Controllers\Client\OrderController::class, 'getPackageDetails'])->name('api.packages.show');
    });

    // Admin only
    Route::prefix('admin')
    ->middleware(['auth', IsAdmin::class])
    ->group(function () {

        Route::get('/', [App\Http\Controllers\AdminDashboardController::class, 'index'])
            ->name('admin.dashboard');

        // semua route admin lain taruh di sini
        Route::resource('clients', App\Http\Controllers\Admin\ClientController::class)
            ->names('admin.clients');
        
        // Client password reset route
        Route::put('clients/{client}/reset-password', [App\Http\Controllers\Admin\ClientController::class, 'resetPassword'])
            ->name('admin.clients.reset-password');
            
        Route::resource('services', App\Http\Controllers\Admin\ServiceController::class)
            ->names('admin.services');
            
        // Service Packages management
        Route::resource('service-packages', App\Http\Controllers\Admin\ServicePackageController::class)
            ->names('admin.service-packages');
        Route::put('service-packages/{package}/toggle-status', [App\Http\Controllers\Admin\ServicePackageController::class, 'toggleStatus'])
            ->name('admin.service-packages.toggle-status');
        Route::get('api/service-packages/active', [App\Http\Controllers\Admin\ServicePackageController::class, 'getActivePackages'])
            ->name('admin.service-packages.active');
            
        // Invoice management
        Route::resource('invoices', App\Http\Controllers\InvoiceController::class)
            ->names('admin.invoices');
        Route::post('invoices/{invoice}/send', [App\Http\Controllers\InvoiceController::class, 'send'])->name('admin.invoices.send');
        Route::post('invoices/{invoice}/mark-paid', [App\Http\Controllers\InvoiceController::class, 'markAsPaid'])->name('admin.invoices.mark-paid');
        
        // Status update routes
        Route::put('invoices/{invoice}/update-status', [App\Http\Controllers\AdminDashboardController::class, 'updateInvoiceStatus'])
            ->name('admin.invoices.update-status');
        Route::put('services/{service}/update-status', [App\Http\Controllers\AdminDashboardController::class, 'updateServiceStatus'])
            ->name('admin.services.update-status');
            
        // Invoice edit routes
        Route::put('invoices/{invoice}/quick-update', [App\Http\Controllers\InvoiceController::class, 'updateInvoice'])
            ->name('admin.invoices.quick-update');
        Route::put('invoices/{invoice}/status', [App\Http\Controllers\InvoiceController::class, 'updateStatus'])
            ->name('admin.invoices.status-update');
        Route::delete('invoices/{invoice}/delete', [App\Http\Controllers\InvoiceController::class, 'deleteInvoice'])
            ->name('admin.invoices.delete');
        Route::put('invoices/{invoice}/service-link', [App\Http\Controllers\InvoiceController::class, 'updateServiceLink'])
            ->name('admin.invoices.update-service-link');
        Route::get('api/clients/{client}/services', [App\Http\Controllers\InvoiceController::class, 'getClientServices'])
            ->name('admin.invoices.client-services');
            
        // Client management routes
        Route::put('clients/{client}/toggle-status', [App\Http\Controllers\Admin\ClientController::class, 'toggleStatus'])
            ->name('admin.clients.toggle-status');
        Route::post('clients/{client}/services', [App\Http\Controllers\Admin\ClientController::class, 'manageServices'])
            ->name('admin.clients.manage-services');
        Route::get('clients/{client}/services', [App\Http\Controllers\Admin\ClientController::class, 'getServices'])
            ->name('admin.clients.get-services');
        Route::delete('services/{service}', [App\Http\Controllers\Admin\ClientController::class, 'deleteService'])
            ->name('admin.services.delete');
            
        // Service details management routes
        Route::get('services/{service}/manage-details', [App\Http\Controllers\Admin\ServiceController::class, 'manageDetails'])
            ->name('admin.services.manage-details');
        Route::put('services/{service}/update-details', [App\Http\Controllers\Admin\ServiceController::class, 'updateDetails'])
            ->name('admin.services.update-details');
    ///akhir darigrp admin
    });
    // Payment routes
    Route::prefix('payment')->name('payment.')->group(function () {
        Route::get('/invoice/{invoice}', [App\Http\Controllers\PaymentController::class, 'show'])->name('show');
        Route::post('/invoice/{invoice}/process', [App\Http\Controllers\PaymentController::class, 'process'])->name('process');
        Route::get('/invoice/{invoice}/status', [App\Http\Controllers\PaymentController::class, 'checkStatus'])->name('status');
        Route::post('/invoice/{invoice}/cancel', [App\Http\Controllers\PaymentController::class, 'cancel'])->name('cancel');
    });
    
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password.update');
    
    // Service upgrade routes
    Route::post('/services/{service}/upgrade-request', [App\Http\Controllers\ServiceUpgradeController::class, 'submitRequest'])->name('services.upgrade.request');
    Route::get('/services/{service}/upgrade-status', [App\Http\Controllers\ServiceUpgradeController::class, 'checkUpgradeStatus'])->name('services.upgrade.status');
    Route::get('/upgrade-requests', [App\Http\Controllers\ServiceUpgradeController::class, 'clientRequests'])->name('client.upgrade-requests.index');
    Route::get('/upgrade-requests/{request}', [App\Http\Controllers\ServiceUpgradeController::class, 'clientShow'])->name('client.upgrade-requests.show');
    Route::post('/upgrade-requests/{upgradeRequest}/cancel', [App\Http\Controllers\ServiceUpgradeController::class, 'cancel'])->name('client.upgrade-requests.cancel');
});

// Public payment routes (no auth required for callbacks)
Route::post('/payment/callback', [App\Http\Controllers\PaymentController::class, 'callback'])->name('payment.callback');
Route::get('/payment/return', [App\Http\Controllers\PaymentController::class, 'return'])->name('payment.return');

// Google OAuth2 routes
Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('google.auth');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback'])->name('google.callback');
Route::get('/gmail-test', function () { return view('gmail-test'); })->name('gmail.test')->middleware('auth');
Route::post('/test-gmail-api', [GoogleAuthController::class, 'sendTestEmail'])->name('test.gmail.api')->middleware('auth');

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Service upgrade requests management
    Route::get('/upgrade-requests', [App\Http\Controllers\Admin\ServiceUpgradeController::class, 'index'])->name('upgrade-requests.index');
    Route::get('/upgrade-requests/{upgradeRequest}', [App\Http\Controllers\Admin\ServiceUpgradeController::class, 'show'])->name('upgrade-requests.show');
    Route::post('/upgrade-requests/{upgradeRequest}/approve', [App\Http\Controllers\Admin\ServiceUpgradeController::class, 'approve'])->name('upgrade-requests.approve');
    Route::post('/upgrade-requests/{upgradeRequest}/reject', [App\Http\Controllers\Admin\ServiceUpgradeController::class, 'reject'])->name('upgrade-requests.reject');
    Route::post('/upgrade-requests/{upgradeRequest}/processing', [App\Http\Controllers\Admin\ServiceUpgradeController::class, 'markAsProcessing'])->name('upgrade-requests.processing');
    Route::post('/upgrade-requests/bulk-action', [App\Http\Controllers\Admin\ServiceUpgradeController::class, 'bulkAction'])->name('upgrade-requests.bulk-action');
});

// Test routes (remove in production)
Route::get('/test/payment/config', [App\Http\Controllers\TestPaymentController::class, 'testConfig']);
Route::get('/test/payment/create', [App\Http\Controllers\TestPaymentController::class, 'testPayment']);
Route::post('/test/payment/callback', [App\Http\Controllers\TestPaymentController::class, 'testCallback']);

// Simple debug routes
Route::get('/debug/connection', [App\Http\Controllers\SimpleTestController::class, 'testConnection']);
Route::get('/debug/api-call', [App\Http\Controllers\SimpleTestController::class, 'testApiCall']);
