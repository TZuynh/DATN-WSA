<?php

use App\Http\Controllers\Admin\SinhVienController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])->name('admin.')->group(function () {
    Route::resource('/admin/sinh-vien', SinhVienController::class)->except(['show']);
    Route::post('/admin/sinh-vien/import', [SinhVienController::class, 'import'])->name('sinh-vien.import');
    Route::delete('sinh-vien/bulk-delete', [SinhVienController::class, 'bulkDelete'])->name('sinh-vien.bulkDelete');
}); 