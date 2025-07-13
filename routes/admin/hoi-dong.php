<?php

use App\Http\Controllers\Admin\HoiDongController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/hoi-dong', [HoiDongController::class, 'index'])->name('admin.hoi-dong.index');
    Route::get('/admin/hoi-dong/create', [HoiDongController::class, 'create'])->name('admin.hoi-dong.create');
    Route::post('/admin/hoi-dong', [HoiDongController::class, 'store'])->name('admin.hoi-dong.store');
    Route::get('/admin/hoi-dong/{hoiDong}', [HoiDongController::class, 'show'])->name('admin.hoi-dong.show');
    Route::get('/admin/hoi-dong/{hoiDong}/edit', [HoiDongController::class, 'edit'])->name('admin.hoi-dong.edit');
    Route::put('/admin/hoi-dong/{hoiDong}', [HoiDongController::class, 'update'])->name('admin.hoi-dong.update');
    Route::delete('/admin/hoi-dong/{hoiDong}', [HoiDongController::class, 'destroy'])->name('admin.hoi-dong.destroy');
    
    // Xóa đề tài khỏi hội đồng
    Route::delete('/admin/hoi-dong/{hoiDong}/de-tai/{deTai}', [HoiDongController::class, 'xoaDeTai'])->name('admin.hoi-dong.xoa-de-tai');
    
    // Chuyển đề tài sang hội đồng khác
    Route::post('/admin/hoi-dong/chuyen-de-tai', [HoiDongController::class, 'chuyenDeTaiSangHoiDong'])->name('admin.hoi-dong.chuyen-de-tai');
    Route::post('/admin/hoi-dong/{hoiDong}/them-de-tai', [HoiDongController::class, 'themDeTai'])->name('admin.hoi-dong.them-de-tai');
    
    // Lấy dữ liệu thành viên hội đồng
    Route::get('/admin/hoi-dong/{hoiDong}/thanh-vien', [HoiDongController::class, 'getThanhVienHoiDong'])->name('admin.hoi-dong.thanh-vien');
    
    // Debug: Kiểm tra vai trò giảng viên
    Route::post('/admin/hoi-dong/debug-vai-tro', [HoiDongController::class, 'debugVaiTroGiangVien'])->name('admin.hoi-dong.debug-vai-tro');
});
