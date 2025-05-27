<?php

use App\Http\Controllers\Admin\DotBaoCaoController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dot-bao-cao', [DotBaoCaoController::class, 'index'])->name('admin.dot-bao-cao.index');
    Route::get('/dot-bao-cao/create', [DotBaoCaoController::class, 'create'])->name('admin.dot-bao-cao.create');
    Route::post('/dot-bao-cao', [DotBaoCaoController::class, 'store'])->name('admin.dot-bao-cao.store');
    Route::get('/dot-bao-cao/{dotBaoCao}/edit', [DotBaoCaoController::class, 'edit'])->name('admin.dot-bao-cao.edit');
    Route::put('/dot-bao-cao/{dotBaoCao}', [DotBaoCaoController::class, 'update'])->name('admin.dot-bao-cao.update');
    Route::delete('/dot-bao-cao/{dotBaoCao}', [DotBaoCaoController::class, 'destroy'])->name('admin.dot-bao-cao.destroy');
}); 