<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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

// Common login routes for project.test
Route::domain('project.test')->group(function () {
    require __DIR__ . '/auth.php';
});

// Admin subdomain routes
Route::domain('admin.project.test')->group(function () {
    require __DIR__ . '/admin/dashboard.php';
    require __DIR__ . '/admin/taikhoan.php';
    require __DIR__ . '/admin/phan-cong-hoi-dong.php';
    require __DIR__ . '/admin/hoi-dong.php';
    require __DIR__ . '/admin/dot-bao-cao.php';
    require __DIR__ . '/admin/cai-dat.php';
    require __DIR__ . '/admin/de-tai.php';
    require __DIR__ . '/admin/api-doc.php';
    require __DIR__ . '/admin/lich-cham.php';
    require __DIR__ . '/admin/sinh-vien.php';
    require __DIR__ . '/admin/nhom.php';
    require __DIR__ . '/admin/lop.php';
    require __DIR__ . '/admin/phan-cong-cham.php';
    require __DIR__ . '/admin/phong.php';
    require __DIR__ . '/admin/bang-diem.php';

    Route::get('/', function () {
        if (Auth::check() && Auth::user()->vai_tro === 'admin') {
            return redirect('/dashboard');
        }
        return redirect('http://project.test');
    });
});

// Giang vien subdomain routes
Route::domain('giangvien.project.test')->group(function () {
    require __DIR__ . '/giangvien/dashboard.php';
    require __DIR__ . '/giangvien/sinh-vien.php';
    require __DIR__ . '/giangvien/nhom.php';
    require __DIR__ . '/giangvien/profile.php';
    require __DIR__ . '/giangvien/de-tai.php';
    require __DIR__ . '/giangvien/lop.php';
    require __DIR__ . '/giangvien/bang-diem.php';

    Route::get('/', function () {
        if (Auth::check() && Auth::user()->vai_tro === 'giang_vien') {
            return redirect('/dashboard');
        }
        return redirect('http://project.test');
    });
});

Route::get('/', function () {
    return view('welcome');
});
