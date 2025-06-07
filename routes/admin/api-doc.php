<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ApiDocController;

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/api-doc', [ApiDocController::class, 'index'])->name('api-doc.index');
}); 