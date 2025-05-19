<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::domain('admin.project.test')->group(function () {
    require __DIR__ . '/admin/auth.php';
    require __DIR__ . '/admin/dashboard.php';
    require __DIR__ . '/admin/taikhoan.php';
});

//Route::domain('giangvien.project.test')->group(function () {
//    require __DIR__ . '/giangvien/auth.php';
//    require __DIR__ . '/giangvien/dashboard.php';
//});

Route::get('/', function () {
    return view('welcome');
});
