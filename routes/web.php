<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\DotBaoCaoController;

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
    
    Route::get('/', function () {
        if (Auth::check() && Auth::user()->vai_tro === 'admin') {
            return redirect('/dashboard');
        }
        return redirect('http://project.test');
    });

    Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
        Route::get('dot-bao-cao/{dotBaoCao}', [DotBaoCaoController::class, 'show'])->name('dot-bao-cao.show');
    });
});

// Giang vien subdomain routes
Route::domain('giangvien.project.test')->group(function () {
    require __DIR__ . '/giangvien/dashboard.php';
    require __DIR__ . '/giangvien/dang-ky.php';
    require __DIR__ . '/giangvien/sinh-vien.php';
    
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
