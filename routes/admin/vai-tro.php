<?php

use App\Http\Controllers\Admin\VaiTroController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin/vai-tro')->name('admin.vai-tro.')->group(function () {
    Route::get('/', [VaiTroController::class, 'index'])->name('index');
    Route::get('/create', [VaiTroController::class, 'create'])->name('create');
    Route::post('/', [VaiTroController::class, 'store'])->name('store');
    Route::get('/{vaiTro}/edit', [VaiTroController::class, 'edit'])->name('edit');
    Route::put('/{vaiTro}', [VaiTroController::class, 'update'])->name('update');
    Route::delete('/{vaiTro}', [VaiTroController::class, 'destroy'])->name('destroy');
}); 