<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ApiAuthController;
use App\Http\Controllers\Admin\ApiDocController;

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
Route::prefix('admin')->middleware(['auth:sanctum', 'role:admin'])->group(function () {
    // API Documentation
    Route::get('/api-doc', [ApiDocController::class, 'index']);
    
    // API Đề tài
    Route::get('/de-tai', [ApiDocController::class, 'getDeTai']);
    Route::get('/de-tai/{id}', [ApiDocController::class, 'showDeTai']);
    Route::post('/de-tai', [ApiDocController::class, 'storeDeTai']);
    Route::match(['put', 'patch'], '/de-tai/{id}', [ApiDocController::class, 'updateDeTai']);
    Route::delete('/de-tai/{id}', [ApiDocController::class, 'destroyDeTai']);
});

// Giang vien API routes
Route::prefix('giang-vien')->middleware(['auth:sanctum', 'role:giangvien'])->group(function () {
    // Thêm các route API cho giảng viên ở đây
    // Ví dụ:
    // Route::prefix('sinh-vien')->group(function () {
    //     Route::get('/', [SinhVienController::class, 'index']);
    //     Route::post('/', [SinhVienController::class, 'store']);
    // });
});
