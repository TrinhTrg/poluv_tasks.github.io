<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public routes (no authentication required)
Route::get('/v1/auth/abilities', [\App\Http\Controllers\API\AuthController::class, 'abilities']);
Route::post('/v1/auth/login', [\App\Http\Controllers\API\AuthController::class, 'login']);

// Protected routes - hỗ trợ cả session auth (web) và token auth (API clients)
// Sanctum sẽ tự động check session nếu request từ same-origin, hoặc Bearer token nếu có
Route::middleware(['web', 'auth:sanctum'])->prefix('v1')->group(function () {
    // Auth routes (chỉ cần token cho external clients, session cho web app)
    Route::get('/auth/user', [\App\Http\Controllers\API\AuthController::class, 'user']);
    Route::get('/auth/tokens', [\App\Http\Controllers\API\AuthController::class, 'tokens']);
    Route::post('/auth/logout', [\App\Http\Controllers\API\AuthController::class, 'logout']);
    Route::post('/auth/logout-all', [\App\Http\Controllers\API\AuthController::class, 'logoutAll']);
    Route::delete('/auth/tokens/{tokenId}', [\App\Http\Controllers\API\AuthController::class, 'revokeToken']);

    // Resource routes - hoạt động với cả session (web app) và token (API clients)
    Route::apiResource('categories', \App\Http\Controllers\API\CategoryController::class);
    Route::apiResource('tasks', \App\Http\Controllers\API\TaskController::class);
    Route::post('tasks/{task}/toggle', [\App\Http\Controllers\API\TaskController::class, 'toggle']);
});

// Legacy route (for backward compatibility, uses Sanctum)
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware(['web', 'auth:sanctum']);
