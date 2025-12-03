<?php

use App\Http\Controllers\v1\BankAccountController;
use App\Http\Controllers\v1\BrandController;
use App\Http\Controllers\v1\CategoryController;
use App\Http\Controllers\v1\MenuController;
use App\Http\Controllers\v1\OrderController;
use App\Http\Controllers\v1\StoreController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group.
|
*/

// API v1 Routes
Route::prefix('v1')->group(function () {
    // Brands Routes
    Route::prefix('brands')->group(function () {
        Route::get('/', [BrandController::class, 'index']);
        Route::post('/', [BrandController::class, 'store']);
        Route::get('/{brand}', [BrandController::class, 'show']);
        Route::put('/{brand}', [BrandController::class, 'update']);
        Route::patch('/{brand}', [BrandController::class, 'update']);
        Route::delete('/{brand}', [BrandController::class, 'destroy']);
        Route::post('/{id}/restore', [BrandController::class, 'restore']);
    });

    // Stores Routes
    Route::prefix('stores')->group(function () {
        Route::get('/', [StoreController::class, 'index']);
        Route::post('/', [StoreController::class, 'store']);
        Route::get('/{store}', [StoreController::class, 'show']);
        Route::put('/{store}', [StoreController::class, 'update']);
        Route::patch('/{store}', [StoreController::class, 'update']);
        Route::delete('/{store}', [StoreController::class, 'destroy']);
        Route::post('/{id}/restore', [StoreController::class, 'restore']);
        Route::get('/{store}/available-menus', [StoreController::class, 'availableMenus']);
    });

    // Categories Routes
    Route::prefix('categories')->group(function () {
        Route::get('/', [CategoryController::class, 'index']);
        Route::post('/', [CategoryController::class, 'store']);
        Route::get('/{category}', [CategoryController::class, 'show']);
        Route::put('/{category}', [CategoryController::class, 'update']);
        Route::patch('/{category}', [CategoryController::class, 'update']);
        Route::delete('/{category}', [CategoryController::class, 'destroy']);
        Route::post('/{id}/restore', [CategoryController::class, 'restore']);
    });

    // Menus Routes
    Route::prefix('menus')->group(function () {
        Route::get('/', [MenuController::class, 'index']);
        Route::post('/', [MenuController::class, 'store']);
        Route::get('/{menu}', [MenuController::class, 'show']);
        Route::put('/{menu}', [MenuController::class, 'update']);
        Route::patch('/{menu}', [MenuController::class, 'update']);
        Route::delete('/{menu}', [MenuController::class, 'destroy']);
        Route::post('/{id}/restore', [MenuController::class, 'restore']);
        Route::get('/{menu}/available-stores', [MenuController::class, 'availableStores']);
        Route::get('/{menu}/stores/{store}/check-availability', [MenuController::class, 'checkAvailability']);
        Route::put('/{menu}/stores/{store}/availability', [MenuController::class, 'updateStoreAvailability']);
        Route::patch('/{menu}/stores/{store}/availability', [MenuController::class, 'updateStoreAvailability']);
    });

    // Bank Accounts Routes
    Route::prefix('bank-accounts')->group(function () {
        Route::get('/', [BankAccountController::class, 'index']);
        Route::post('/', [BankAccountController::class, 'store']);
        Route::get('/{bankAccount}', [BankAccountController::class, 'show']);
        Route::put('/{bankAccount}', [BankAccountController::class, 'update']);
        Route::patch('/{bankAccount}', [BankAccountController::class, 'update']);
        Route::delete('/{bankAccount}', [BankAccountController::class, 'destroy']);
        Route::post('/{id}/restore', [BankAccountController::class, 'restore']);
        Route::get('/{bankAccount}/balance', [BankAccountController::class, 'getBalance']);
        Route::post('/{bankAccount}/balance', [BankAccountController::class, 'updateBalance']);
        Route::put('/{bankAccount}/balance', [BankAccountController::class, 'updateBalance']);
    });

    // Orders Routes
    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index']);
        Route::post('/', [OrderController::class, 'store']);
        Route::get('/check-incomplete', [OrderController::class, 'checkIncompleteOrder']);
        // Specific routes must come before parameterized routes
        Route::post('/{orderNumber}/cancel', [OrderController::class, 'cancel'])->where('orderNumber', '[A-Z0-9-]+');
        Route::post('/{id}/restore', [OrderController::class, 'restore']);
        Route::post('/{order}/items', [OrderController::class, 'addItem']);
        Route::put('/{order}/items/{orderDetail}', [OrderController::class, 'updateItem']);
        Route::patch('/{order}/items/{orderDetail}', [OrderController::class, 'updateItem']);
        Route::delete('/{order}/items/{orderDetail}', [OrderController::class, 'removeItem']);
        // General routes
        Route::get('/{order}', [OrderController::class, 'show']);
        Route::put('/{order}', [OrderController::class, 'update']);
        Route::patch('/{order}', [OrderController::class, 'update']);
        Route::delete('/{order}', [OrderController::class, 'destroy']);
    });
});

