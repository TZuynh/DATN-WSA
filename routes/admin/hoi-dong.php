<?php

use App\Http\Controllers\Admin\HoiDongController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/hoi-dong', [HoiDongController::class, 'index'])->name('admin.hoi-dong.index');
    Route::get('/admin/hoi-dong/create', [HoiDongController::class, 'create'])->name('admin.hoi-dong.create');
    Route::post('/admin/hoi-dong', [HoiDongController::class, 'store'])->name('admin.hoi-dong.store');
    Route::get('/admin/hoi-dong/{hoiDong}', [HoiDongController::class, 'show'])->name('admin.hoi-dong.show');
    Route::get('/admin/hoi-dong/{hoiDong}/edit', [HoiDongController::class, 'edit'])->name('admin.hoi-dong.edit');
    Route::put('/admin/hoi-dong/{hoiDong}', [HoiDongController::class, 'update'])->name('admin.hoi-dong.update');
    Route::delete('/admin/hoi-dong/{hoiDong}', [HoiDongController::class, 'destroy'])->name('admin.hoi-dong.destroy');

    // Route cho đề tài
    Route::post('/admin/hoi-dong/{hoiDong}/de-tai', [HoiDongController::class, 'themDeTai'])->name('admin.hoi-dong.them-de-tai');
    Route::delete('/admin/hoi-dong/{hoiDong}/de-tai/{chiTietBaoCao}', [HoiDongController::class, 'xoaDeTai'])->name('admin.hoi-dong.xoa-de-tai');
    
    // Route cho lịch chấm
    Route::post('/admin/hoi-dong/{hoiDong}/lich-cham', [HoiDongController::class, 'themLichCham'])->name('admin.hoi-dong.them-lich-cham');
    Route::delete('/admin/hoi-dong/{hoiDong}/lich-cham/{lichCham}', [HoiDongController::class, 'xoaLichCham'])->name('admin.hoi-dong.xoa-lich-cham');
}); 