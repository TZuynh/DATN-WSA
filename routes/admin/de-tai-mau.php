<?php

use App\Http\Controllers\Admin\DeTaiMauController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/de-tai-mau', [DeTaiMauController::class, 'index'])->name('admin.de-tai-mau.index');
    Route::get('/admin/de-tai-mau/create', [DeTaiMauController::class, 'create'])->name('admin.de-tai-mau.create');
    Route::post('/admin/de-tai-mau', [DeTaiMauController::class, 'store'])->name('admin.de-tai-mau.store');
    Route::get('/admin/de-tai-mau/{deTaiMau}/edit', [DeTaiMauController::class, 'edit'])->name('admin.de-tai-mau.edit');
    Route::put('/admin/de-tai-mau/{deTaiMau}', [DeTaiMauController::class, 'update'])->name('admin.de-tai-mau.update');
    Route::delete('/admin/de-tai-mau/{deTaiMau}', [DeTaiMauController::class, 'destroy'])->name('admin.de-tai-mau.destroy');
    Route::post('/admin/de-tai-mau/import', [DeTaiMauController::class, 'import'])->name('admin.de-tai-mau.import');
}); 