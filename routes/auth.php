<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\CommonAuthController;
use App\Http\Controllers\Auth\AdminAuthController;

// Route cho trang chủ và đăng nhập
Route::get('/', [CommonAuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [CommonAuthController::class, 'login'])->name('common.login');

// Route cho đăng xuất
Route::get('/logout', [CommonAuthController::class, 'logout'])->name('logout');