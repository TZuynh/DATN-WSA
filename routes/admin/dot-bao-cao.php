<?php

use App\Http\Controllers\Admin\DotBaoCaoController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dot-bao-cao', [DotBaoCaoController::class, 'index'])->name('admin.dot-bao-cao.index');
    Route::get('/admin/dot-bao-cao/create', [DotBaoCaoController::class, 'create'])->name('admin.dot-bao-cao.create');
    Route::post('/admin/dot-bao-cao', [DotBaoCaoController::class, 'store'])->name('admin.dot-bao-cao.store');
    Route::get('/admin/dot-bao-cao/{dotBaoCao}/edit', [DotBaoCaoController::class, 'edit'])->name('admin.dot-bao-cao.edit');
    Route::put('/admin/dot-bao-cao/{dotBaoCao}', [DotBaoCaoController::class, 'update'])->name('admin.dot-bao-cao.update');
    Route::delete('/admin/dot-bao-cao/{dotBaoCao}', [DotBaoCaoController::class, 'destroy'])->name('admin.dot-bao-cao.destroy');
    Route::post('/admin/dot-bao-cao/update-status', [DotBaoCaoController::class, 'updateStatus'])->name('admin.dot-bao-cao.update-status');
    Route::get('/admin/dot-bao-cao/{dotBaoCao}', [DotBaoCaoController::class, 'show'])->name('admin.dot-bao-cao.show');
});
