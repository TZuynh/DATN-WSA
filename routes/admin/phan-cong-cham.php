<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PhanCongChamController;

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/phan-bien', [PhanCongChamController::class, 'index'])->name('admin.phan-cong-cham.index');
    Route::get('/admin/phan-bien/create', [PhanCongChamController::class, 'create'])->name('admin.phan-cong-cham.create');
    Route::post('/admin/phan-bien', [PhanCongChamController::class, 'store'])->name('admin.phan-cong-cham.store');
    Route::get('/admin/phan-bien/{id}/edit', [PhanCongChamController::class, 'edit'])->name('admin.phan-cong-cham.edit');
    Route::put('/admin/phan-bien/{id}', [PhanCongChamController::class, 'update'])->name('admin.phan-cong-cham.update');
    Route::delete('/admin/phan-bien/{id}', [PhanCongChamController::class, 'destroy'])->name('admin.phan-cong-cham.destroy');
    Route::post('/admin/phan-bien/get-hoi-dong-info', [PhanCongChamController::class, 'getHoiDongInfo'])->name('admin.phan-cong-cham.getHoiDongInfo');
}); 