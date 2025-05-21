<?php

use App\Http\Controllers\Admin\HoiDongController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::prefix('hoidong')->name('hoidong.')->group(function () {
        // Hiển thị danh sách đợt chấm
        Route::get('/', [HoiDongController::class, 'index'])->name('index');
        
        // Thêm đợt chấm mới
        Route::post('/store', [HoiDongController::class, 'store'])->name('store');
        
        // Hiển thị form sửa đợt chấm
        Route::get('/edit/{id}', [HoiDongController::class, 'edit'])->name('edit');
        
        // Cập nhật đợt chấm
        Route::put('/update/{id}', [HoiDongController::class, 'update'])->name('update');
        
        // Xóa đợt chấm
        Route::delete('/destroy/{id}', [HoiDongController::class, 'destroy'])->name('destroy');
    });
});

