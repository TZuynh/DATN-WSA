<?php

use App\Http\Controllers\Admin\LopController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])->name('admin.')->group(function () {
    Route::resource('/admin/lop', LopController::class)->except(['show']);
    Route::post('/admin/lop/bulk-delete', [LopController::class, 'bulkDelete'])->name('lop.bulk-delete');
    Route::post('/admin/lop/import', [LopController::class, 'import'])->name('lop.import');
}); 