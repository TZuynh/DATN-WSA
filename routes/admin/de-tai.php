<?php

use App\Http\Controllers\Admin\DeTaiController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/de-tai', [DeTaiController::class, 'index'])->name('admin.de-tai.index');
    Route::get('/admin/de-tai/create', [DeTaiController::class, 'create'])->name('admin.de-tai.create');
    Route::post('/admin/de-tai', [DeTaiController::class, 'store'])->name('admin.de-tai.store');
    Route::get('/admin/de-tai/{deTai}/edit', [DeTaiController::class, 'edit'])->name('admin.de-tai.edit');
    Route::put('/admin/de-tai/{deTai}', [DeTaiController::class, 'update'])->name('admin.de-tai.update');
    Route::delete('/admin/de-tai/{deTai}', [DeTaiController::class, 'destroy'])->name('admin.de-tai.destroy');
});