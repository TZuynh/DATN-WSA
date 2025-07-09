<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PhanCongChamController;

Route::prefix('admin/phan-cong-cham')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('giang-vien-hoi-dong/{de_tai_id}', [PhanCongChamController::class, 'getGiangVienHoiDong'])->name('admin.phan-cong-cham.giang-vien-hoi-dong');
    Route::get('/', [PhanCongChamController::class, 'index'])->name('admin.phan-cong-cham.index');
    Route::get('create', [PhanCongChamController::class, 'create'])->name('admin.phan-cong-cham.create');
    Route::post('/', [PhanCongChamController::class, 'store'])->name('admin.phan-cong-cham.store');
    Route::get('{id}/edit', [PhanCongChamController::class, 'edit'])->name('admin.phan-cong-cham.edit');
    Route::put('{id}', [PhanCongChamController::class, 'update'])->name('admin.phan-cong-cham.update');
    Route::delete('{id}', [PhanCongChamController::class, 'destroy'])->name('admin.phan-cong-cham.destroy');
    Route::post('get-hoi-dong-info', [PhanCongChamController::class, 'getHoiDongInfo'])->name('admin.phan-cong-cham.getHoiDongInfo');
    Route::get('phan-bien', [PhanCongChamController::class, 'phanCongPhanBien'])->name('admin.phan-cong-cham.phan-bien');
    Route::post('phan-bien', [PhanCongChamController::class, 'storePhanBien'])->name('admin.phan-cong-cham.phan-bien.store');
    Route::get('phan-cong-cham/giang-vien-hoi-dong/{de_tai_id}', [PhanCongChamController::class, 'getGiangVienHoiDong']);
}); 