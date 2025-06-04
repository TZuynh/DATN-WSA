<?php

use App\Http\Controllers\GiangVien\DeTaiController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:giang_vien'])->name('giangvien.')->group(function () {
    Route::resource('/giang-vien/de-tai', DeTaiController::class)->except(['show']);
    Route::post('/giang-vien/de-tai/import', [DeTaiController::class, 'import'])->name('de-tai.import');
}); 