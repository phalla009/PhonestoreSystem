<?php

use App\Http\Controllers\Api\CategoryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\PaymentController;

Route::get('products/kr/{id}', [ProductController::class, 'showById']);
Route::apiResource('products/kr', ProductController::class);
Route::apiResource('category/kr',CategoryController::class );

// Route::get('payments/kr', [PaymentController::class, 'createPayment']);
// Route::post('payment/callback', [PaymentController::class, 'paymentCallback'])->name('api.payment.callback');