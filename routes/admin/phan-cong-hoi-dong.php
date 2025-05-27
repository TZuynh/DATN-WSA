<?php

use App\Http\Controllers\Admin\PhanCongHoiDongController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/phan-cong-hoi-dong', [PhanCongHoiDongController::class, 'index'])->name('admin.phan-cong-hoi-dong.index');
    Route::get('/phan-cong-hoi-dong/create', [PhanCongHoiDongController::class, 'create'])->name('admin.phan-cong-hoi-dong.create');
    Route::post('/phan-cong-hoi-dong', [PhanCongHoiDongController::class, 'store'])->name('admin.phan-cong-hoi-dong.store');
    Route::get('/phan-cong-hoi-dong/{phanCongVaiTro}/edit', [PhanCongHoiDongController::class, 'edit'])->name('admin.phan-cong-hoi-dong.edit');
    Route::put('/phan-cong-hoi-dong/{phanCongVaiTro}', [PhanCongHoiDongController::class, 'update'])->name('admin.phan-cong-hoi-dong.update');
    Route::delete('/phan-cong-hoi-dong/{phanCongVaiTro}', [PhanCongHoiDongController::class, 'destroy'])->name('admin.phan-cong-hoi-dong.destroy');
}); 