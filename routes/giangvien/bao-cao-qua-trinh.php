<?php

use App\Http\Controllers\GiangVien\BaoCaoQuaTrinhController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:giang_vien'])->name('giangvien.')->group(function () {
    Route::get('/giang-vien/bao-cao-qua-trinh', [BaoCaoQuaTrinhController::class, 'index'])->name('bao-cao-qua-trinh.index');
    Route::get('/giang-vien/bao-cao-qua-trinh/create', [BaoCaoQuaTrinhController::class, 'create'])->name('bao-cao-qua-trinh.create');
    Route::post('/giang-vien/bao-cao-qua-trinh', [BaoCaoQuaTrinhController::class, 'store'])->name('bao-cao-qua-trinh.store');
    Route::get('/giang-vien/bao-cao-qua-trinh/{baoCaoQuaTrinh}/edit', [BaoCaoQuaTrinhController::class, 'edit'])->name('bao-cao-qua-trinh.edit');
    Route::put('/giang-vien/bao-cao-qua-trinh/{baoCaoQuaTrinh}', [BaoCaoQuaTrinhController::class, 'update'])->name('bao-cao-qua-trinh.update');
    Route::delete('/giang-vien/bao-cao-qua-trinh/{baoCaoQuaTrinh}', [BaoCaoQuaTrinhController::class, 'destroy'])->name('bao-cao-qua-trinh.destroy');
    Route::get('/giang-vien/bao-cao-qua-trinh/{baoCaoQuaTrinh}', [BaoCaoQuaTrinhController::class, 'show'])->name('bao-cao-qua-trinh.show');
}); 