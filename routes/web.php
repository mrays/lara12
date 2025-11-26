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
    Route::middleware(['auth', 'role:client'])->prefix('client')->name('client.')->group(function () {
        Route::get('/', [ClientDashboardController::class, 'index'])->name('dashboard');
        Route::get('/invoices', [App\Http\Controllers\InvoiceController::class, 'clientInvoices'])->name('invoices.index');
        Route::get('/invoices/{invoice}', [App\Http\Controllers\InvoiceController::class, 'clientShow'])->name('invoices.show');
        Route::get('/invoices/{invoice}/pdf', [App\Http\Controllers\InvoiceController::class, 'downloadPDF'])->name('invoices.pdf');
        Route::get('/invoices/{invoice}/pay', [App\Http\Controllers\PaymentController::class, 'show'])->name('invoices.pay');
        
        // Client service management routes
        Route::get('/services', [App\Http\Controllers\ServiceManagementController::class, 'index'])->name('services.index');
        Route::get('/services/{service}/manage', [App\Http\Controllers\ServiceManagementController::class, 'show'])->name('services.manage');
        Route::post('/services/{service}/renewal', [App\Http\Controllers\ServiceManagementController::class, 'createRenewalInvoice'])->name('services.renewal');
        Route::post('/services/{service}/update', [App\Http\Controllers\ServiceManagementController::class, 'update'])->name('services.update');
        Route::post('/services/{service}/support', [App\Http\Controllers\ServiceManagementController::class, 'contactSupport'])->name('services.support');
        
        // Client order routes
        Route::get('/orders/create', [App\Http\Controllers\Client\OrderController::class, 'create'])->name('orders.create');
        Route::post('/orders', [App\Http\Controllers\Client\OrderController::class, 'store'])->name('orders.store');
        Route::get('/orders/{invoice}/success', [App\Http\Controllers\Client\OrderController::class, 'success'])->name('orders.success');
        
        // Client upgrade requests
        Route::get('/upgrade-requests', [App\Http\Controllers\ServiceUpgradeController::class, 'clientRequests'])->name('upgrade-requests.index');
        Route::get('/upgrade-requests/{request}', [App\Http\Controllers\ServiceUpgradeController::class, 'clientShow'])->name('upgrade-requests.show');
        Route::post('/upgrade-requests/{upgradeRequest}/cancel', [App\Http\Controllers\ServiceUpgradeController::class, 'cancel'])->name('upgrade-requests.cancel');
    });

    // API routes for client (outside client prefix for easier access)
    Route::middleware(['auth', 'role:client'])->prefix('api')->name('api.')->group(function () {
        Route::get('/packages/{id}', [App\Http\Controllers\Client\OrderController::class, 'getPackageDetails'])->name('packages.show');
        Route::get('/check-domain', [App\Http\Controllers\Client\OrderController::class, 'checkDomain'])->name('check-domain')->middleware('throttle:10,1'); // 10 requests per minute
        Route::get('/clients/{client}/services', [App\Http\Controllers\InvoiceController::class, 'getClientServices']);
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
            
        // Server management - Explicit routes first to avoid conflicts
        Route::get('servers', [App\Http\Controllers\Admin\ServerController::class, 'index'])
            ->name('admin.servers.index');
        Route::get('servers/create', [App\Http\Controllers\Admin\ServerController::class, 'create'])
            ->name('admin.servers.create');
        Route::post('servers', [App\Http\Controllers\Admin\ServerController::class, 'store'])
            ->name('admin.servers.store');
        
        // Specific server routes (before wildcard {server} routes)
        Route::get('servers/{server}/password', [App\Http\Controllers\Admin\ServerController::class, 'getPassword'])
            ->name('admin.servers.password');
        Route::get('servers/{server}/edit', [App\Http\Controllers\Admin\ServerController::class, 'edit'])
            ->name('admin.servers.edit');
        Route::put('servers/{server}/toggle-status', [App\Http\Controllers\Admin\ServerController::class, 'toggleStatus'])
            ->name('admin.servers.toggle-status');
        
        // Generic server routes (after specific routes)
        Route::get('servers/{server}', [App\Http\Controllers\Admin\ServerController::class, 'show'])
            ->name('admin.servers.show');
        Route::put('servers/{server}', [App\Http\Controllers\Admin\ServerController::class, 'update'])
            ->name('admin.servers.update');
        Route::delete('servers/{server}', [App\Http\Controllers\Admin\ServerController::class, 'destroy'])
            ->name('admin.servers.destroy');
            
        // Domain Registers management
        Route::get('domain-registers/{register}/send-reminder', [App\Http\Controllers\Admin\DomainRegisterController::class, 'sendReminder'])
            ->name('admin.domain-registers.send-reminder');
        
        // Domain management
        Route::get('domains', [App\Http\Controllers\Admin\DomainController::class, 'index'])
            ->name('admin.domains.index');
        Route::get('domains/create', [App\Http\Controllers\Admin\DomainController::class, 'create'])
            ->name('admin.domains.create');
        Route::post('domains', [App\Http\Controllers\Admin\DomainController::class, 'store'])
            ->name('admin.domains.store');
        Route::get('domains/{domainId}/edit', [App\Http\Controllers\Admin\DomainController::class, 'edit'])
            ->name('admin.domains.edit');
        Route::put('domains/{domainId}', [App\Http\Controllers\Admin\DomainController::class, 'update'])
            ->name('admin.domains.update');
        Route::delete('domains/{domainId}', [App\Http\Controllers\Admin\DomainController::class, 'destroy'])
            ->name('admin.domains.destroy');
        Route::post('domains/{domainId}/send-reminder', [App\Http\Controllers\Admin\DomainController::class, 'sendReminder'])
            ->name('admin.domains.send-reminder');
        Route::post('domains/export', [App\Http\Controllers\Admin\DomainController::class, 'export'])
            ->name('admin.domains.export');
        Route::get('domain-registers/{register}/password', [App\Http\Controllers\Admin\DomainRegisterController::class, 'getPassword'])
            ->name('admin.domain-registers.password');
        Route::put('domain-registers/{register}/toggle-status', [App\Http\Controllers\Admin\DomainRegisterController::class, 'toggleStatus'])
            ->name('admin.domain-registers.toggle-status');
        
        // Explicit domain-registers routes to fix parameter issue
        Route::get('domain-registers', [App\Http\Controllers\Admin\DomainRegisterController::class, 'index'])
            ->name('admin.domain-registers.index');
        Route::get('domain-registers/create', [App\Http\Controllers\Admin\DomainRegisterController::class, 'create'])
            ->name('admin.domain-registers.create');
        Route::post('domain-registers', [App\Http\Controllers\Admin\DomainRegisterController::class, 'store'])
            ->name('admin.domain-registers.store');
        Route::get('domain-registers/{register}', [App\Http\Controllers\Admin\DomainRegisterController::class, 'show'])
            ->name('admin.domain-registers.show');
        Route::get('domain-registers/{registerId}/edit', [App\Http\Controllers\Admin\DomainRegisterController::class, 'edit'])
            ->name('admin.domain-registers.edit');
        Route::put('domain-registers/{registerId}', [App\Http\Controllers\Admin\DomainRegisterController::class, 'update'])
            ->name('admin.domain-registers.update');
        Route::post('domain-registers/bulk-delete', [App\Http\Controllers\Admin\DomainRegisterController::class, 'bulkDelete'])
            ->name('admin.domain-registers.bulk-delete');
        Route::delete('domain-registers/{register}', [App\Http\Controllers\Admin\DomainRegisterController::class, 'destroy'])
            ->name('admin.domain-registers.destroy');
            
        // Client Data management
        Route::get('client-data/service-status', [App\Http\Controllers\Admin\ClientDataController::class, 'serviceStatus'])
            ->name('admin.client-data.service-status');
        Route::post('client-data/export', [App\Http\Controllers\Admin\ClientDataController::class, 'export'])
            ->name('admin.client-data.export');
        
        // Explicit client-data routes to fix parameter issue
        Route::get('client-data', [App\Http\Controllers\Admin\ClientDataController::class, 'index'])
            ->name('admin.client-data.index');
        Route::get('client-data/create', [App\Http\Controllers\Admin\ClientDataController::class, 'create'])
            ->name('admin.client-data.create');
        Route::post('client-data', [App\Http\Controllers\Admin\ClientDataController::class, 'store'])
            ->name('admin.client-data.store');
        Route::get('client-data/{client}', [App\Http\Controllers\Admin\ClientDataController::class, 'show'])
            ->name('admin.client-data.show');
        Route::get('client-data/{client}/edit', [App\Http\Controllers\Admin\ClientDataController::class, 'edit'])
            ->name('admin.client-data.edit');
        Route::put('client-data/{client}', [App\Http\Controllers\Admin\ClientDataController::class, 'update'])
            ->name('admin.client-data.update');
        Route::delete('client-data/{client}', [App\Http\Controllers\Admin\ClientDataController::class, 'destroy'])
            ->name('admin.client-data.destroy');
            
        // Invoice edit routes
        Route::put('invoices/{invoice}/quick-update', [App\Http\Controllers\InvoiceController::class, 'updateInvoice'])
            ->name('admin.invoices.quick-update');
        Route::put('invoices/{invoice}/status', [App\Http\Controllers\InvoiceController::class, 'updateStatus'])
            ->name('admin.invoices.status');
            
        // Domain Extensions management
        Route::put('domain-extensions/{domainExtension}/toggle-status', [App\Http\Controllers\Admin\DomainExtensionController::class, 'toggleStatus'])
            ->name('admin.domain-extensions.toggle-status');
        Route::resource('domain-extensions', App\Http\Controllers\Admin\DomainExtensionController::class)
            ->names('admin.domain-extensions')
            ->parameters(['domain_extensions' => 'domainExtension']);
        
        
        // Invoice management routes
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
            
        // Bulk Delete Routes
        Route::post('invoices/bulk-delete', [App\Http\Controllers\InvoiceController::class, 'bulkDelete'])
            ->name('admin.invoices.bulk-delete');
        Route::post('domain-extensions/bulk-delete', [App\Http\Controllers\Admin\DomainExtensionController::class, 'bulkDelete'])
            ->name('admin.domain-extensions.bulk-delete');
        Route::post('service-packages/bulk-delete', [App\Http\Controllers\Admin\ServicePackageController::class, 'bulkDelete'])
            ->name('admin.service-packages.bulk-delete');
        Route::post('services/bulk-delete', [App\Http\Controllers\Admin\ServiceController::class, 'bulkDelete'])
            ->name('admin.services.bulk-delete');
        Route::post('clients/bulk-delete', [App\Http\Controllers\Admin\ClientController::class, 'bulkDelete'])
            ->name('admin.clients.bulk-delete');
        Route::post('upgrade-requests/bulk-delete', [App\Http\Controllers\Admin\ServiceUpgradeController::class, 'bulkDelete'])
            ->name('admin.upgrade-requests.bulk-delete');
        Route::delete('upgrade-requests/{upgradeRequest}', [App\Http\Controllers\Admin\ServiceUpgradeController::class, 'destroy'])
            ->name('admin.upgrade-requests.destroy');
        Route::post('client-data/bulk-delete', [App\Http\Controllers\Admin\ClientDataController::class, 'bulkDelete'])
            ->name('admin.client-data.bulk-delete');
        Route::post('domains/bulk-delete', [App\Http\Controllers\Admin\DomainController::class, 'bulkDelete'])
            ->name('admin.domains.bulk-delete');
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
    Route::post('/services/{service}/cancellation-request', [App\Http\Controllers\ServiceUpgradeController::class, 'submitCancellationRequest'])->name('services.cancellation.request');
    Route::get('/services/{service}/upgrade-status', [App\Http\Controllers\ServiceUpgradeController::class, 'checkUpgradeStatus'])->name('services.upgrade.status');
});

// Public payment routes (no auth required for callbacks)
Route::post('/payment/callback', [App\Http\Controllers\PaymentController::class, 'callback'])->name('payment.callback');
Route::get('/payment/return', [App\Http\Controllers\PaymentController::class, 'return'])->name('payment.return');

// Guest ordering routes (no auth required)
Route::prefix('order')->name('order.')->group(function () {
    Route::get('/choose-domain', [App\Http\Controllers\OrderController::class, 'chooseDomain'])->name('choose-domain');
    Route::post('/select-domain', [App\Http\Controllers\OrderController::class, 'selectDomain'])->name('select-domain');
    Route::get('/select-template', [App\Http\Controllers\OrderController::class, 'selectTemplate'])->name('select-template');
    Route::post('/select-template', [App\Http\Controllers\OrderController::class, 'postSelectTemplate'])->name('post-select-template');
    Route::get('/customer-details', [App\Http\Controllers\OrderController::class, 'customerDetails'])->name('customer-details');
    Route::post('/customer-details', [App\Http\Controllers\OrderController::class, 'registerCustomer'])->name('register-customer');
    Route::get('/select-package', [App\Http\Controllers\OrderController::class, 'selectPackage'])->name('select-package');
    Route::post('/submit-order', [App\Http\Controllers\OrderController::class, 'submitOrder'])->name('submit-order');
});

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

// Debug route for checking user role and middleware
Route::get('/debug/admin-check', function () {
    $user = auth()->user();
    return response()->json([
        'authenticated' => !is_null($user),
        'user_id' => $user?->id,
        'user_name' => $user?->name,
        'user_role' => $user?->role,
        'is_admin' => $user?->role === 'admin',
        'all_middleware' => app('router')->getRoutes()->getByName('admin.domain-extensions.index')?->middleware()
    ]);
})->middleware('auth');

// Test route to verify domain extensions works
Route::get('/debug/domain-test', function () {
    return response()->json([
        'message' => 'Domain extensions route works!',
        'route_exists' => app('router')->getRoutes()->getByName('admin.domain-extensions.index') !== null,
        'current_url' => url()->current(),
        'intended_url' => route('admin.domain-extensions.index')
    ]);
})->middleware(['auth', 'role:admin']);
