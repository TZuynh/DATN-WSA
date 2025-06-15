<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PhongController;

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/phong', [PhongController::class, 'index'])->name('admin.phong.index');
    Route::get('/admin/phong/create', [PhongController::class, 'create'])->name('admin.phong.create');
    Route::post('/admin/phong', [PhongController::class, 'store'])->name('admin.phong.store');
    Route::get('/admin/phong/{phong}/edit', [PhongController::class, 'edit'])->name('admin.phong.edit');
    Route::put('/admin/phong/{phong}', [PhongController::class, 'update'])->name('admin.phong.update');
    Route::delete('/admin/phong/{phong}', [PhongController::class, 'destroy'])->name('admin.phong.destroy');
    Route::post('/admin/phong/import', [PhongController::class, 'import'])->name('admin.phong.import');
}); 