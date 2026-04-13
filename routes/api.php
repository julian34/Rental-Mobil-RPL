<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;

// Public routes
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// Protected routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);
    
    // User role management
    Route::post('/users/{user}/assign-role', [AuthController::class, 'assignRole']);
    Route::post('/users/{user}/remove-role', [AuthController::class, 'removeRole']);
    Route::get('/users/{user}/roles', [AuthController::class, 'getUserRoles']);
});

// Admin only routes
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
});
