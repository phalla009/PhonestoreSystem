<?php

use App\Http\Controllers\LoginController; 
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserManagerController;
use Illuminate\Support\Facades\Route;

// Public auth routes
Route::get('/', [LoginController::class, 'showLoginForm']);
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login'); 
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


// Protected routes — require login
Route::middleware('auth')->group(function () { 

    // Dashboard home
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Resource controllers
    Route::resource('products', ProductController::class)->names('products');
    Route::resource('orders', OrderController::class)->names('orders');
    Route::resource('categories', CategoryController::class)->names('categories');
    Route::resource('customers', CustomerController::class)->names('customers');
    Route::resource('inventorys', InventoryController::class)->names('inventorys');
    Route::resource('payments', PaymentController::class)->names('payments');
    Route::resource('userroles', RoleController::class)->parameters([
        'userroles' => 'role'
    ]);

    Route::resource('usermanagers', UserManagerController::class);
    // Reports
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::post('reports/generate', [ReportController::class, 'generate'])->name('reports.generate');

    // Payment actions
    Route::get('/payments/payment/{order}', [PaymentController::class, 'payment'])->name('payments.payment');
    Route::post('/payments/store', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('/payments/search', [PaymentController::class, 'search'])->name('payments.search');
});
