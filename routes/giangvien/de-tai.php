<?php

use App\Http\Controllers\GiangVien\DeTaiController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:giang_vien'])->name('giangvien.')->group(function () {
    Route::get('/giang-vien/de-tai', [DeTaiController::class, 'index'])->name('de-tai.index');
    Route::get('/giang-vien/de-tai/create', [DeTaiController::class, 'create'])->name('de-tai.create');
    Route::post('/giang-vien/de-tai', [DeTaiController::class, 'store'])->name('de-tai.store');
    Route::get('/giang-vien/de-tai/{deTai}/edit', [DeTaiController::class, 'edit'])->name('de-tai.edit');
    Route::put('/giang-vien/de-tai/{deTai}', [DeTaiController::class, 'update'])->name('de-tai.update');
    Route::delete('/giang-vien/de-tai/{deTai}', [DeTaiController::class, 'destroy'])->name('de-tai.destroy');
    Route::get('/giang-vien/de-tai/{deTai}/export-pdf', [DeTaiController::class, 'exportPdfDetail'])->name('de-tai.export-pdf-detail');
    Route::get('/giang-vien/de-tai/{deTai}/preview-pdf', [DeTaiController::class, 'previewPdfDetail'])->name('de-tai.preview-pdf-detail');
}); 