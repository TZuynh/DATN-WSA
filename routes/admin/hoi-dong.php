<?php

use App\Http\Controllers\Admin\HoiDongController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/hoi-dong', [HoiDongController::class, 'index'])->name('admin.hoi-dong.index');
    Route::get('/hoi-dong/create', [HoiDongController::class, 'create'])->name('admin.hoi-dong.create');
    Route::post('/hoi-dong', [HoiDongController::class, 'store'])->name('admin.hoi-dong.store');
    Route::get('/hoi-dong/{hoiDong}/edit', [HoiDongController::class, 'edit'])->name('admin.hoi-dong.edit');
    Route::put('/hoi-dong/{hoiDong}', [HoiDongController::class, 'update'])->name('admin.hoi-dong.update');
    Route::delete('/hoi-dong/{hoiDong}', [HoiDongController::class, 'destroy'])->name('admin.hoi-dong.destroy');
}); 