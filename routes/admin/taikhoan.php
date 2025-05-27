<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\TaiKhoanController;

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/tai-khoan', [TaiKhoanController::class, 'index'])->name('admin.taikhoan.index');
    Route::get('/tai-khoan/create', [TaiKhoanController::class, 'create'])->name('admin.taikhoan.create');
    Route::post('/tai-khoan', [TaiKhoanController::class, 'store'])->name('admin.taikhoan.store');
    Route::get('/tai-khoan/{id}/edit', [TaiKhoanController::class, 'edit'])->name('admin.taikhoan.edit');
    Route::put('/tai-khoan/{id}', [TaiKhoanController::class, 'update'])->name('admin.taikhoan.update');
    Route::delete('/tai-khoan/{id}', [TaiKhoanController::class, 'destroy'])->name('admin.taikhoan.destroy');
    Route::post('/tai-khoan/import', [TaiKhoanController::class, 'import'])->name('admin.taikhoan.import');
});
