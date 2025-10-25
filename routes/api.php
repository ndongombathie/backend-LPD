<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StoreController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\TransferController;
use App\Http\Controllers\Api\DashboardController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/refresh', [AuthController::class, 'refresh']);

    // Store routes
    Route::apiResource('stores', StoreController::class);
    Route::get('/stores/{store}/products', [StoreController::class, 'products']);

    // Product routes
    Route::apiResource('products', ProductController::class);
    Route::patch('/products/{product}/stock', [ProductController::class, 'updateStock']);

    // Order routes
    Route::apiResource('orders', OrderController::class);
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel']);
    Route::get('/orders/pending', [OrderController::class, 'pendingOrders']);

    // Payment routes
    Route::apiResource('payments', PaymentController::class);
    Route::post('/payments/{payment}/complete', [PaymentController::class, 'complete']);

    // Transfer routes
    Route::apiResource('transfers', TransferController::class);
    Route::post('/transfers/{transfer}/approve', [TransferController::class, 'approve']);
    Route::post('/transfers/{transfer}/reject', [TransferController::class, 'reject']);

    // Dashboard routes
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
    Route::get('/dashboard/sales', [DashboardController::class, 'sales']);
    Route::get('/dashboard/products', [DashboardController::class, 'products']);
    Route::get('/dashboard/orders', [DashboardController::class, 'orders']);
});
