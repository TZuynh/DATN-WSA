<?php

use App\Http\Controllers\GiangVien\LopController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:giang_vien'])->name('giangvien.')->group(function () {
    Route::resource('/giang-vien/lop', LopController::class)->except(['show']);
    Route::post('/giang-vien/lop/bulk-delete', [LopController::class, 'bulkDelete'])->name('lop.bulk-delete');
}); 