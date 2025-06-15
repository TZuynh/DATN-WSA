<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PhanCongChamController;

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/phan-cong-cham', [PhanCongChamController::class, 'index'])->name('admin.phan-cong-cham.index');
    Route::get('/admin/phan-cong-cham/create', [PhanCongChamController::class, 'create'])->name('admin.phan-cong-cham.create');
    Route::post('/admin/phan-cong-cham', [PhanCongChamController::class, 'store'])->name('admin.phan-cong-cham.store');
    Route::get('/admin/phan-cong-cham/{id}/edit', [PhanCongChamController::class, 'edit'])->name('admin.phan-cong-cham.edit');
    Route::put('/admin/phan-cong-cham/{id}', [PhanCongChamController::class, 'update'])->name('admin.phan-cong-cham.update');
    Route::delete('/admin/phan-cong-cham/{id}', [PhanCongChamController::class, 'destroy'])->name('admin.phan-cong-cham.destroy');
}); 