<?php

use App\Http\Controllers\Admin\TaiKhoanController;
use Illuminate\Support\Facades\Route;

Route::get('/admin/tai-khoan', [TaiKhoanController::class, 'index'])->name('admin.taikhoan.index');
Route::post('/admin/tai-khoan/import', [TaiKhoanController::class, 'import'])->name('admin.taikhoan.import');
