<?php

use App\Http\Controllers\GiangVien\DangKyController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:giang_vien'])->group(function () {
    Route::get('/dang-ky', [DangKyController::class, 'index'])->name('giangvien.dang-ky.index');
    Route::get('/dang-ky/create', [DangKyController::class, 'create'])->name('giangvien.dang-ky.create');
    Route::post('/dang-ky', [DangKyController::class, 'store'])->name('giangvien.dang-ky.store');
    Route::get('/dang-ky/{dangKy}/edit', [DangKyController::class, 'edit'])->name('giangvien.dang-ky.edit');
    Route::put('/dang-ky/{dangKy}', [DangKyController::class, 'update'])->name('giangvien.dang-ky.update');
    Route::delete('/dang-ky/{dangKy}', [DangKyController::class, 'destroy'])->name('giangvien.dang-ky.destroy');
}); 