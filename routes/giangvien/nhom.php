<?php
use App\Http\Controllers\GiangVien\NhomController;
use Illuminate\Support\Facades\Route;

Route::prefix('nhom')->name('giangvien.nhom.')->middleware(['auth', 'role:giang_vien'])->group(function () {
    Route::get('/', [NhomController::class, 'index'])->name('index');
    Route::get('/create', [NhomController::class, 'create'])->name('create');
    Route::post('/store', [NhomController::class, 'store'])->name('store');
    Route::get('/{nhom}/edit', [NhomController::class, 'edit'])->name('edit');
    Route::put('/{nhom}', [NhomController::class, 'update'])->name('update');
    Route::delete('/{nhom}', [NhomController::class, 'destroy'])->name('destroy');
    Route::post('/import', [NhomController::class, 'import'])->name('import');
    Route::get('/download-template', [NhomController::class, 'downloadTemplate'])->name('download-template');
});
