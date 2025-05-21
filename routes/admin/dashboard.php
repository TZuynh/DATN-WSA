<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ProfileController;

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    Route::get('/admin/profile', [ProfileController::class, 'index'])->name('admin.profile');
});
