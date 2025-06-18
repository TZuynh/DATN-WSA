<?php

use App\Http\Controllers\Admin\LichChamController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/lich-bao-ve', [LichChamController::class, 'index'])->name('admin.lich-cham.index');
    Route::get('/admin/lich-bao-ve/create', [LichChamController::class, 'create'])->name('admin.lich-cham.create');
    Route::post('/admin/lich-bao-ve', [LichChamController::class, 'store'])->name('admin.lich-cham.store');
    Route::get('/admin/lich-bao-ve/{lichCham}/edit', [LichChamController::class, 'edit'])->name('admin.lich-cham.edit');
    Route::put('/admin/lich-bao-ve/{lichCham}', [LichChamController::class, 'update'])->name('admin.lich-cham.update');
    Route::delete('/admin/lich-bao-ve/{lichCham}', [LichChamController::class, 'destroy'])->name('admin.lich-cham.destroy');
    Route::get('/admin/lich-bao-ve/export-pdf', [LichChamController::class, 'exportPdf'])->name('admin.lich-cham.export-pdf');
    Route::post('/admin/lich-bao-ve/update-order', [LichChamController::class, 'updateOrder'])->name('admin.lich-cham.update-order');
}); 