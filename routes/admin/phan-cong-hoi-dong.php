<?php

use App\Http\Controllers\Admin\PhanCongHoiDongController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/phan-cong-hoi-dong', [PhanCongHoiDongController::class, 'index'])->name('admin.phan-cong-hoi-dong.index');
    Route::get('/admin/phan-cong-hoi-dong/create', [PhanCongHoiDongController::class, 'create'])->name('admin.phan-cong-hoi-dong.create');
    Route::post('/admin/phan-cong-hoi-dong', [PhanCongHoiDongController::class, 'store'])->name('admin.phan-cong-hoi-dong.store');
    Route::get('/admin/phan-cong-hoi-dong/{thanhVienHoiDong}/edit', [PhanCongHoiDongController::class, 'edit'])->name('admin.phan-cong-hoi-dong.edit');
    Route::put('/admin/phan-cong-hoi-dong/{thanhVienHoiDong}', [PhanCongHoiDongController::class, 'update'])->name('admin.phan-cong-hoi-dong.update');
    Route::delete('/admin/phan-cong-hoi-dong/{thanhVienHoiDong}', [PhanCongHoiDongController::class, 'destroy'])->name('admin.phan-cong-hoi-dong.destroy');
}); 