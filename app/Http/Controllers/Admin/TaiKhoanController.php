<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TaiKhoan;
use Illuminate\Http\Request;

class TaiKhoanController extends Controller
{
    public function index()
    {
        $taikhoans = TaiKhoan::whereIn('vai_tro', ['admin', 'giang_vien'])->get();

        return view('admin.taikhoan.index', compact('taikhoans'));
    }
}
