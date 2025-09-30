<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;

Route::get('products/kr/{id}', [ProductController::class, 'showById']);
Route::apiResource('products/kr', ProductController::class);

