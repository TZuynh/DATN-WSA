<?php

use App\Http\Controllers\Admin\DotBaoCaoController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [DotBaoCaoController::class, 'index'])->name('admin.dot-bao-cao.index');
    Route::get('/create', [DotBaoCaoController::class, 'create'])->name('admin.dot-bao-cao.create');
    Route::post('/store', [DotBaoCaoController::class, 'store'])->name('admin.dot-bao-cao.store');
    Route::get('/{dotBaoCao}/edit', [DotBaoCaoController::class, 'edit'])->name('admin.dot-bao-cao.edit');
    Route::put('/{dotBaoCao}', [DotBaoCaoController::class, 'update'])->name('admin.dot-bao-cao.update');
    Route::delete('/{dotBaoCao}', [DotBaoCaoController::class, 'destroy'])->name('admin.dot-bao-cao.destroy');
}); 