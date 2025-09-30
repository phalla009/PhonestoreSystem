<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
Route::get('products/kr/{id}', [ProductController::class, 'showById']);
Route::apiResource('products/kr', ProductController::class);

