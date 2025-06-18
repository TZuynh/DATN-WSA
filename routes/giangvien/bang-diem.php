<?php

use App\Http\Controllers\GiangVien\BangDiemController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:giang_vien'])->group(function () {
    Route::get('/bang-diem', [BangDiemController::class, 'index'])->name('giangvien.bang-diem.index');
    Route::get('/bang-diem/create/{sinhVienId}/{dotBaoCaoId}', [BangDiemController::class, 'create'])->name('giangvien.bang-diem.create');
    Route::post('/bang-diem/{sinhVienId}/{dotBaoCaoId}', [BangDiemController::class, 'store'])->name('giangvien.bang-diem.store');
    Route::get('/bang-diem/{id}', [BangDiemController::class, 'show'])->name('giangvien.bang-diem.show');
    Route::get('/bang-diem/{id}/edit', [BangDiemController::class, 'edit'])->name('giangvien.bang-diem.edit');
    Route::put('/bang-diem/{id}', [BangDiemController::class, 'update'])->name('giangvien.bang-diem.update');
    Route::delete('/bang-diem/{id}', [BangDiemController::class, 'destroy'])->name('giangvien.bang-diem.destroy');
    
    // Route debug tạm thời
    Route::get('/bang-diem-debug', [BangDiemController::class, 'debug'])->name('giangvien.bang-diem.debug');
    Route::get('/bang-diem-debug-simple', [BangDiemController::class, 'debugSimple'])->name('giangvien.bang-diem.debug-simple');
    Route::get('/bang-diem-debug/{id}', [BangDiemController::class, 'debugBangDiem'])->name('giangvien.bang-diem.debug-bang-diem');
}); 