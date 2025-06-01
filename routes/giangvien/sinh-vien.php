<?php

use App\Http\Controllers\GiangVien\SinhVienController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:giang_vien'])->name('giangvien.')->group(function () {
    Route::resource('/giang-vien/sinh-vien', SinhVienController::class)->except(['show']);
    Route::post('/giang-vien/sinh-vien/import', [SinhVienController::class, 'import'])->name('sinh-vien.import');
});
