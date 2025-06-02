<?php

use App\Http\Controllers\Admin\DangKyController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::prefix('dang-ky')->name('dang-ky.')->group(function () {
        Route::get('/', [DangKyController::class, 'index'])->name('index');
        Route::get('/create', [DangKyController::class, 'create'])->name('create');
        Route::post('/', [DangKyController::class, 'store'])->name('store');
        Route::get('/{dangKy}/edit', [DangKyController::class, 'edit'])->name('edit');
        Route::put('/{dangKy}', [DangKyController::class, 'update'])->name('update');
        Route::delete('/{dangKy}', [DangKyController::class, 'destroy'])->name('destroy');
    });
});