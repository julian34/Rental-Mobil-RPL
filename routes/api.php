<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\CarController;

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

    // Car management
    Route::get('/cars', [CarController::class, 'index']);
    Route::get('/cars/{car}', [CarController::class, 'show']);
    Route::post('/cars', [CarController::class, 'store']);
    Route::put('/cars/{car}', [CarController::class, 'update']);
    Route::delete('/cars/{car}', [CarController::class, 'destroy']);
});
