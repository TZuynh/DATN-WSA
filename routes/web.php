<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\AuthController;

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
    require __DIR__ . '/admin/phan-cong-hoi-dong.php';
    require __DIR__ . '/admin/hoi-dong.php';
    require __DIR__ . '/admin/dot-bao-cao.php';
    require __DIR__ . '/admin/cai-dat.php';
    
    Route::get('/', function () {
        if (Auth::check() && Auth::user()->vai_tro === 'admin') {
            return redirect('/dashboard');
        }
        return redirect()->route('admin.login');
    });
});

//Route::domain('giangvien.project.test')->group(function () {
//    require __DIR__ . '/giangvien/auth.php';
//    require __DIR__ . '/giangvien/dashboard.php';
//});

Route::get('/', function () {
    return view('welcome');
});
