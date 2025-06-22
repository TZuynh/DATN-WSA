<?php

use App\Http\Controllers\Admin\BangDiemController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/bang-diem', [BangDiemController::class, 'index'])->name('admin.bang-diem.index');
    Route::get('/admin/bang-diem/thong-ke', [BangDiemController::class, 'thongKe'])->name('admin.bang-diem.thong-ke');
    Route::get('/admin/bang-diem/{id}', [BangDiemController::class, 'show'])->name('admin.bang-diem.show');
    Route::get('/admin/bang-diem/{id}/edit', [BangDiemController::class, 'edit'])->name('admin.bang-diem.edit');
    Route::put('/admin/bang-diem/{id}', [BangDiemController::class, 'update'])->name('admin.bang-diem.update');
    Route::delete('/admin/bang-diem/{id}', [BangDiemController::class, 'destroy'])->name('admin.bang-diem.destroy');
    Route::get('/admin/bang-diem/export/excel', [BangDiemController::class, 'export'])->name('admin.bang-diem.export');
    
    // Route debug tạm thời
    Route::get('/admin/bang-diem-debug', [BangDiemController::class, 'debug'])->name('admin.bang-diem.debug');
});