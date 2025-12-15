<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::prefix('v1')->group(function () {
    Route::apiResource('categories', \App\Http\Controllers\API\CategoryController::class);
    // Route cho Tasks (bạn đã tạo ở bước trước)
    Route::apiResource('tasks', \App\Http\Controllers\API\TaskController::class);
    // Route để toggle task completion
    Route::post('tasks/{task}/toggle', [\App\Http\Controllers\API\TaskController::class, 'toggle']);
});