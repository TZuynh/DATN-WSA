<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:giang_vien'])->group(function () {
    Route::get('/', function () {
        return view('giangvien.dashboard');
    })->name('giangvien.dashboard');

    // Quản lý sinh viên
    Route::prefix('sinh-vien')->group(function () {
        Route::get('/', [App\Http\Controllers\GiangVien\SinhVienController::class, 'index'])->name('giangvien.sinh-vien.index');
        Route::get('/create', [App\Http\Controllers\GiangVien\SinhVienController::class, 'create'])->name('giangvien.sinh-vien.create');
        Route::post('/', [App\Http\Controllers\GiangVien\SinhVienController::class, 'store'])->name('giangvien.sinh-vien.store');
        Route::get('/{sinhVien}/edit', [App\Http\Controllers\GiangVien\SinhVienController::class, 'edit'])->name('giangvien.sinh-vien.edit');
        Route::put('/{sinhVien}', [App\Http\Controllers\GiangVien\SinhVienController::class, 'update'])->name('giangvien.sinh-vien.update');
        Route::delete('/{sinhVien}', [App\Http\Controllers\GiangVien\SinhVienController::class, 'destroy'])->name('giangvien.sinh-vien.destroy');
    });

    // Quản lý đăng ký
    Route::prefix('dang-ky')->group(function () {
        Route::get('/', [App\Http\Controllers\GiangVien\DangKyController::class, 'index'])->name('giangvien.dang-ky.index');
        Route::get('/create', [App\Http\Controllers\GiangVien\DangKyController::class, 'create'])->name('giangvien.dang-ky.create');
        Route::post('/', [App\Http\Controllers\GiangVien\DangKyController::class, 'store'])->name('giangvien.dang-ky.store');
        Route::get('/{dangKy}/edit', [App\Http\Controllers\GiangVien\DangKyController::class, 'edit'])->name('giangvien.dang-ky.edit');
        Route::put('/{dangKy}', [App\Http\Controllers\GiangVien\DangKyController::class, 'update'])->name('giangvien.dang-ky.update');
        Route::delete('/{dangKy}', [App\Http\Controllers\GiangVien\DangKyController::class, 'destroy'])->name('giangvien.dang-ky.destroy');
    });

    // Quản lý nhóm
    Route::prefix('nhom')->group(function () {
        Route::get('/', [App\Http\Controllers\GiangVien\NhomController::class, 'index'])->name('giangvien.nhom.index');
        Route::get('/create', [App\Http\Controllers\GiangVien\NhomController::class, 'create'])->name('giangvien.nhom.create');
        Route::post('/', [App\Http\Controllers\GiangVien\NhomController::class, 'store'])->name('giangvien.nhom.store');
        Route::get('/{nhom}/edit', [App\Http\Controllers\GiangVien\NhomController::class, 'edit'])->name('giangvien.nhom.edit');
        Route::put('/{nhom}', [App\Http\Controllers\GiangVien\NhomController::class, 'update'])->name('giangvien.nhom.update');
        Route::delete('/{nhom}', [App\Http\Controllers\GiangVien\NhomController::class, 'destroy'])->name('giangvien.nhom.destroy');
    });

    // Quản lý đề tài
    Route::prefix('de-tai')->group(function () {
        Route::get('/', [App\Http\Controllers\GiangVien\DeTaiController::class, 'index'])->name('giangvien.de-tai.index');
        Route::get('/create', [App\Http\Controllers\GiangVien\DeTaiController::class, 'create'])->name('giangvien.de-tai.create');
        Route::post('/', [App\Http\Controllers\GiangVien\DeTaiController::class, 'store'])->name('giangvien.de-tai.store');
        Route::get('/{deTai}/edit', [App\Http\Controllers\GiangVien\DeTaiController::class, 'edit'])->name('giangvien.de-tai.edit');
        Route::put('/{deTai}', [App\Http\Controllers\GiangVien\DeTaiController::class, 'update'])->name('giangvien.de-tai.update');
        Route::delete('/{deTai}', [App\Http\Controllers\GiangVien\DeTaiController::class, 'destroy'])->name('giangvien.de-tai.destroy');
    });

    // Quản lý mẫu đề tài
    Route::prefix('de-tai-mau')->group(function () {
        Route::get('/', [App\Http\Controllers\GiangVien\DeTaiMauController::class, 'index'])->name('giangvien.de-tai-mau.index');
        Route::get('/create', [App\Http\Controllers\GiangVien\DeTaiMauController::class, 'create'])->name('giangvien.de-tai-mau.create');
        Route::post('/', [App\Http\Controllers\GiangVien\DeTaiMauController::class, 'store'])->name('giangvien.de-tai-mau.store');
        Route::post('/import', [App\Http\Controllers\GiangVien\DeTaiMauController::class, 'import'])->name('giangvien.de-tai-mau.import');
        Route::get('/{deTaiMau}/edit', [App\Http\Controllers\GiangVien\DeTaiMauController::class, 'edit'])->name('giangvien.de-tai-mau.edit');
        Route::put('/{deTaiMau}', [App\Http\Controllers\GiangVien\DeTaiMauController::class, 'update'])->name('giangvien.de-tai-mau.update');
        Route::delete('/{deTaiMau}', [App\Http\Controllers\GiangVien\DeTaiMauController::class, 'destroy'])->name('giangvien.de-tai-mau.destroy');
    });
}); 