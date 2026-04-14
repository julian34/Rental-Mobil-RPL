<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\RentalTransactionController;
use App\Http\Controllers\CashierController;

// Public routes
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/register-customer', [AuthController::class, 'registerCustomer']);
Route::post('/auth/login', [AuthController::class, 'login']);

// Protected routes (require Sanctum token)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // User role management (Administrator only)
    Route::middleware('check.role:Administrator')->group(function () {
        Route::post('/users/{user}/assign-role', [AuthController::class, 'assignRole']);
        Route::post('/users/{user}/remove-role', [AuthController::class, 'removeRole']);
    });

    // Any authenticated user can view their own or other users' roles
    Route::get('/users/{user}/roles', [AuthController::class, 'getUserRoles']);
});

// Authenticated routes — any logged-in user can browse cars and manage own transactions
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/cars', [CarController::class, 'index']);
    Route::get('/cars/{car}', [CarController::class, 'show']);

    // Rental transactions (Customer)
    Route::get('/transactions', [RentalTransactionController::class, 'index']);
    Route::post('/transactions', [RentalTransactionController::class, 'store']);
    Route::get('/transactions/{rentalTransaction}', [RentalTransactionController::class, 'show']);
});

// Cashier routes (Cashier + Administrator)
Route::middleware(['auth:sanctum', 'check.role:Cashier,Administrator'])->group(function () {
    Route::get('/cashier/transactions', [CashierController::class, 'index']);
    Route::get('/cashier/stats', [CashierController::class, 'stats']);
    Route::patch('/cashier/transactions/{transaction}/payment', [CashierController::class, 'processPayment']);
    Route::patch('/cashier/transactions/{transaction}/handover', [CashierController::class, 'confirmHandover']);
    Route::patch('/cashier/transactions/{transaction}/return', [CashierController::class, 'confirmReturn']);
    Route::patch('/cashier/transactions/{transaction}/cancel', [CashierController::class, 'cancel']);
});

// Administrator-only routes
Route::middleware(['auth:sanctum', 'check.role:Administrator'])->group(function () {
    // Role management
    Route::get('/roles', [RoleController::class, 'getAllRoles']);
    Route::get('/roles/{role}', [RoleController::class, 'getRoleById']);
    Route::post('/roles', [RoleController::class, 'createRole']);
    Route::put('/roles/{role}', [RoleController::class, 'updateRole']);
    Route::delete('/roles/{role}', [RoleController::class, 'deleteRole']);

    // Permission management
    Route::post('/roles/{role}/permissions', [RoleController::class, 'assignPermissionToRole']);
    Route::delete('/roles/{role}/permissions', [RoleController::class, 'removePermissionFromRole']);

    // Car management (write operations)
    Route::post('/cars', [CarController::class, 'store']);
    Route::put('/cars/{car}', [CarController::class, 'update']);
    Route::delete('/cars/{car}', [CarController::class, 'destroy']);
});
