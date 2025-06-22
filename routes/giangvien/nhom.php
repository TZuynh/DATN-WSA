<?php
use App\Http\Controllers\GiangVien\NhomController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:giang_vien'])->name('giangvien.')->group(function () {
    Route::resource('/giang-vien/nhom', NhomController::class)
        ->parameters(['nhom' => 'nhom'])
        ->except(['show'])
        ->names('nhom');

    Route::post('/giang-vien/nhom/import', [NhomController::class, 'import'])->name('nhom.import');
    Route::get('/giang-vien/nhom/download-template', [NhomController::class, 'downloadTemplate'])->name('nhom.download-template');
    
    Route::get('/giang-vien/nhom/{nhom}/change-detai', [NhomController::class, 'showChangeDeTaiForm'])->name('nhom.changeDetai');
    Route::post('/giang-vien/nhom/{nhom}/change-detai', [NhomController::class, 'changeDeTai'])->name('nhom.changeDetai.submit');
});
