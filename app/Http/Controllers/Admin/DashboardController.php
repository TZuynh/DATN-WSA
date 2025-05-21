<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TaiKhoan;
use App\Models\HoiDong;
use App\Models\DotBaoCao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            // Lấy tổng số tài khoản
            $totalTaiKhoan = DB::table('tai_khoans')->count();

            // Lấy tổng số giảng viên (vai trò = giang_vien)
            $totalGiangVien = DB::table('tai_khoans')
                ->where('vai_tro', 'giang_vien')
                ->count();

            // Lấy tổng số hội đồng
            $totalHoiDong = DB::table('hoi_dongs')->count();

            // Lấy tổng số đợt báo cáo
            $totalDotBaoCao = DB::table('dot_bao_caos')->count();

            return view('admin.dashboard', compact(
                'totalTaiKhoan',
                'totalGiangVien',
                'totalHoiDong',
                'totalDotBaoCao'
            ));
        } catch (\Exception $e) {
            // Nếu có lỗi, trả về view với giá trị mặc định là 0
            return view('admin.dashboard', [
                'totalTaiKhoan' => 0,
                'totalGiangVien' => 0,
                'totalHoiDong' => 0,
                'totalDotBaoCao' => 0
            ]);
        }
    }
} 