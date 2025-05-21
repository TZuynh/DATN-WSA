<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CaiDatController;

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/caidat', [CaiDatController::class, 'index'])->name('admin.cai-dat.index');
    Route::post('/admin/caidat/background', [CaiDatController::class, 'updateBackground'])->name('admin.cai-dat.update-background');
    Route::post('/admin/caidat/general', [CaiDatController::class, 'updateGeneral'])->name('admin.cai-dat.update-general');
    Route::post('/admin/caidat/seo', [CaiDatController::class, 'updateSeo'])->name('admin.cai-dat.update-seo');
    Route::post('/admin/caidat/security', [CaiDatController::class, 'updateSecurity'])->name('admin.cai-dat.update-security');
}); 