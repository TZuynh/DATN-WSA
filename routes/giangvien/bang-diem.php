<?php

use App\Http\Controllers\GiangVien\BangDiemController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:giang_vien'])->group(function () {
    Route::get('/giang-vien/bang-diem', [BangDiemController::class, 'index'])->name('giangvien.bang-diem.index');
    Route::get('/giang-vien/bang-diem/create/{sinhVienId}/{dotBaoCaoId?}', [BangDiemController::class, 'create'])->name('giangvien.bang-diem.create');
    Route::post('/giang-vien/bang-diem/store', [BangDiemController::class, 'store'])->name('giangvien.bang-diem.store');
    Route::get('/giang-vien/bang-diem/{id}', [BangDiemController::class, 'show'])->name('giangvien.bang-diem.show');
    Route::get('/giang-vien/bang-diem/{id}/edit', [BangDiemController::class, 'edit'])->name('giangvien.bang-diem.edit');
    Route::put('/giang-vien/bang-diem/{id}', [BangDiemController::class, 'update'])->name('giangvien.bang-diem.update');
    Route::delete('/giang-vien/bang-diem/{id}', [BangDiemController::class, 'destroy'])->name('giangvien.bang-diem.destroy');

    Route::get('/giang-vien/bang-diem-hoi-dong', [App\Http\Controllers\GiangVien\BangDiemController::class, 'councilIndex'])->name('giangvien.bang-diem-hoi-dong.index');

    // Route debug tạm thời
//    Route::get('/giang-vien/bang-diem-debug', [BangDiemController::class, 'debug'])->name('giangvien.bang-diem.debug');
//    Route::get('/giang-vien/bang-diem-debug-simple', [BangDiemController::class, 'debugSimple'])->name('giangvien.bang-diem.debug-simple');
//    Route::get('/giang-vien/bang-diem-debug/{id}', [BangDiemController::class, 'debugBangDiem'])->name('giangvien.bang-diem.debug-bang-diem');
});
