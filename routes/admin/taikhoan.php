<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\TaiKhoanController;

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/tai-khoan', [TaiKhoanController::class, 'index'])->name('admin.taikhoan.index');
    Route::get('/admin/tai-khoan/create', [TaiKhoanController::class, 'create'])->name('admin.taikhoan.create');
    Route::post('/admin/tai-khoan', [TaiKhoanController::class, 'store'])->name('admin.taikhoan.store');
    Route::get('/admin/tai-khoan/{id}/edit', [TaiKhoanController::class, 'edit'])->name('admin.taikhoan.edit');
    Route::put('/admin/tai-khoan/{id}', [TaiKhoanController::class, 'update'])->name('admin.taikhoan.update');
    Route::delete('/admin/tai-khoan/{id}', [TaiKhoanController::class, 'destroy'])->name('admin.taikhoan.destroy');
    Route::post('/admin/tai-khoan/import', [TaiKhoanController::class, 'import'])->name('admin.taikhoan.import');
});
