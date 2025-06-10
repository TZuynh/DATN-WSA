<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ApiAuthController;

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

// Auth routes cho tất cả các domain
Route::post('/auth/login', [ApiAuthController::class, 'login']);

// Protected routes cho tất cả các domain
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [ApiAuthController::class, 'logout']);
    Route::get('/auth/user', [ApiAuthController::class, 'user']);
});

// Admin API routes
Route::domain('admin.project.test')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        // Thêm các route API cho admin ở đây
        // Ví dụ:
        // Route::prefix('de-tai')->group(function () {
        //     Route::get('/', [DeTaiController::class, 'index']);
        //     Route::post('/', [DeTaiController::class, 'store']);
        // });
    });
});

// Giang vien API routes
Route::domain('giangvien.project.test')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        // Thêm các route API cho giảng viên ở đây
        // Ví dụ:
        // Route::prefix('sinh-vien')->group(function () {
        //     Route::get('/', [SinhVienController::class, 'index']);
        //     Route::post('/', [SinhVienController::class, 'store']);
        // });
    });
});
