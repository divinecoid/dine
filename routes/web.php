<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PublicMenuController;
use App\Http\Controllers\PublicOrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [RegistrationController::class, 'store'])->name('register.store');

// Registration flow routes
Route::prefix('registration')->name('registration.')->group(function () {
    Route::get('/verify/{registration}', [RegistrationController::class, 'showVerify'])->name('verify');
    Route::post('/verify/{registration}', [RegistrationController::class, 'verifyOTP'])->name('verify.otp');
    Route::post('/resend-otp/{registration}', [RegistrationController::class, 'resendOTP'])->name('resend-otp');
    Route::get('/payment/{registration}', [RegistrationController::class, 'showPayment'])->name('payment');
    Route::get('/payment/{registration}/generate', [RegistrationController::class, 'generatePayment'])->name('payment.generate');
    Route::get('/payment/{registration}/status', [RegistrationController::class, 'checkPaymentStatus'])->name('payment.status');
    Route::post('/complete/{registration}', [RegistrationController::class, 'completeRegistration'])->name('complete');
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin Routes (Protected)
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin.role'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Kasir Routes
    Route::get('/kasir', [\App\Http\Controllers\Admin\KasirController::class, 'index'])->name('kasir.index');
    Route::post('/kasir/order', [\App\Http\Controllers\Admin\KasirController::class, 'store'])->name('kasir.order.store');
    Route::post('/kasir/order/{order}/payment', [\App\Http\Controllers\Admin\KasirController::class, 'processPayment'])->name('kasir.order.payment');
    Route::post('/kasir/order/{order}/generate-qris', [\App\Http\Controllers\Admin\KasirController::class, 'generateQRIS'])->name('kasir.order.generate-qris');
    Route::post('/kasir/order/{order}/complete', [\App\Http\Controllers\Admin\KasirController::class, 'completeOrder'])->name('kasir.order.complete');
    Route::get('/kasir/payment/{payment}/status', [\App\Http\Controllers\Admin\KasirController::class, 'checkPaymentStatus'])->name('kasir.payment.status');
    
    // CRUD Routes
    Route::resource('brands', \App\Http\Controllers\Admin\BrandController::class);
    Route::resource('stores', \App\Http\Controllers\Admin\StoreController::class);
    Route::resource('tables', \App\Http\Controllers\Admin\TableController::class);
    Route::post('tables/{table}/close-orders', [\App\Http\Controllers\Admin\TableController::class, 'closeOrders'])->name('tables.close-orders');
    Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);
    Route::resource('menus', \App\Http\Controllers\Admin\MenuController::class);
    
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    
    Route::resource('bank-accounts', \App\Http\Controllers\Admin\BankAccountController::class);
    Route::post('bank-accounts/{bankAccount}/verify', [\App\Http\Controllers\Admin\BankAccountController::class, 'verify'])->name('bank-accounts.verify');
    
    Route::get('/feedback', function () {
        return view('admin.feedback.index');
    })->name('feedback.index');
    
    Route::get('/withdrawals', function () {
        return view('admin.withdrawals.index');
    })->name('withdrawals.index');
});

// Public order routes
Route::prefix('orders')->name('public.orders.')->group(function () {
    // Order list for table
    Route::get('/', [PublicOrderController::class, 'index'])->name('index');
    // Close all orders for table
    Route::post('/close-table', [PublicOrderController::class, 'closeTable'])->name('close-table');
    // Generate QRIS for payment
    Route::get('/{orderNumber}/qris', [PublicOrderController::class, 'generateQRIS'])
        ->where('orderNumber', '[A-Z0-9-]+')
        ->name('qris');
    // Check payment status
    Route::get('/{orderNumber}/payment-status', [PublicOrderController::class, 'checkPaymentStatus'])
        ->where('orderNumber', '[A-Z0-9-]+')
        ->name('payment-status');
    // Process payment for order
    Route::post('/{orderNumber}/pay', [PublicOrderController::class, 'processPayment'])
        ->where('orderNumber', '[A-Z0-9-]+')
        ->name('pay');
    // Order detail by order number
    Route::get('/{orderNumber}', [PublicOrderController::class, 'show'])
        ->where('orderNumber', '[A-Z0-9-]+')
        ->name('show');
});

// Legacy route for order detail (backward compatibility)
Route::get('/order/{orderNumber}', [PublicOrderController::class, 'show'])
    ->where('orderNumber', '[A-Z0-9-]+')
    ->name('public.order');

// Xendit Webhook (no CSRF protection needed)
Route::post('/webhooks/xendit', [\App\Http\Controllers\XenditWebhookController::class, 'handleWebhook'])
    ->name('webhooks.xendit');

// Public menu view for QR code access
// URL pattern: /{brand-slug}?table_id={unique_identifier}
Route::get('/{brandSlug}', [PublicMenuController::class, 'show'])
    ->where('brandSlug', '[a-z0-9-]+')
    ->name('public.menu');
