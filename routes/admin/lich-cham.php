<?php

use App\Http\Controllers\Admin\LichChamController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/lich-cham', [LichChamController::class, 'index'])->name('admin.lich-cham.index');
    Route::get('/admin/lich-cham/create', [LichChamController::class, 'create'])->name('admin.lich-cham.create');
    Route::post('/admin/lich-cham', [LichChamController::class, 'store'])->name('admin.lich-cham.store');
    Route::get('/admin/lich-cham/{lichCham}/edit', [LichChamController::class, 'edit'])->name('admin.lich-cham.edit');
    Route::put('/admin/lich-cham/{lichCham}', [LichChamController::class, 'update'])->name('admin.lich-cham.update');
    Route::delete('/admin/lich-cham/{lichCham}', [LichChamController::class, 'destroy'])->name('admin.lich-cham.destroy');
}); 