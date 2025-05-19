<?php

use App\Http\Controllers\Admin\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/', [AuthController::class, 'login'])->name('admin.login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');
