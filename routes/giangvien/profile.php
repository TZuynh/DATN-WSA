<?php

use App\Http\Controllers\GiangVien\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get('/giangvien/profile', [ProfileController::class, 'index'])->name('giangvien.profile');
    Route::put('/giangvien/profile', [ProfileController::class, 'update'])->name('giangvien.profile.update');
    Route::put('/giangvien/profile/change-password', [ProfileController::class, 'changePassword'])->name('giangvien.profile.change-password');
}); 