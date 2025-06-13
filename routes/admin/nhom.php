<?php

use App\Http\Controllers\Admin\NhomController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])->name('admin.')->group(function () {
    Route::resource('/admin/nhom', NhomController::class)->except(['show']);
    Route::post('/admin/nhom/import', [NhomController::class, 'import'])->name('nhom.import');
    Route::get('/admin/nhom/download-template', [NhomController::class, 'downloadTemplate'])->name('nhom.download-template');
}); 