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
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin Routes (Protected)
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin.role'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
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
    // Order detail by order number
    Route::get('/{orderNumber}', [PublicOrderController::class, 'show'])
        ->where('orderNumber', '[A-Z0-9-]+')
        ->name('show');
});

// Legacy route for order detail (backward compatibility)
Route::get('/order/{orderNumber}', [PublicOrderController::class, 'show'])
    ->where('orderNumber', '[A-Z0-9-]+')
    ->name('public.order');

// Public menu view for QR code access
// URL pattern: /{brand-slug}?table_id={unique_identifier}
Route::get('/{brandSlug}', [PublicMenuController::class, 'show'])
    ->where('brandSlug', '[a-z0-9-]+')
    ->name('public.menu');
