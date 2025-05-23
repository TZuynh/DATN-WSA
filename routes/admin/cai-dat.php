<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CaiDatController;

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/caidat', [CaiDatController::class, 'index'])->name('admin.cai-dat.index');
    Route::post('/caidat/background', [CaiDatController::class, 'updateBackground'])->name('admin.cai-dat.update-background');
    Route::post('/caidat/general', [CaiDatController::class, 'updateGeneral'])->name('admin.cai-dat.update-general');
    Route::post('/caidat/seo', [CaiDatController::class, 'updateSeo'])->name('admin.cai-dat.update-seo');
    Route::post('/caidat/security', [CaiDatController::class, 'updateSecurity'])->name('admin.cai-dat.update-security');
}); 