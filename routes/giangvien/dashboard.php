<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GiangVien\DashboardController;

Route::middleware(['auth', 'role:giang_vien'])->group(function () {
    Route::get('/giang-vien/dashboard', [DashboardController::class, 'index'])->name('giangvien.dashboard');
});
