<?php

use App\Http\Controllers\GiangVien\DeTaiMauController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:giang_vien'])->name('giangvien.')->group(function () {
    Route::resource('/giang-vien/de-tai-mau', DeTaiMauController::class)->except(['show']);
    Route::post('/giang-vien/de-tai-mau/import', [DeTaiMauController::class, 'import'])->name('de-tai-mau.import');
}); 