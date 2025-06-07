<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use App\Models\SinhVien;
use App\Models\TaiKhoan;
use App\Models\Nhom;
use App\Models\DeTai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            // Lấy ID của giảng viên đang đăng nhập
            $giangVienId = auth()->user()->id;

            // Tổng số sinh viên
            $totalSinhVien = SinhVien::count();

            // Tổng số giảng viên
            $totalGiangVien = TaiKhoan::where('vai_tro', 'giang_vien')->count();

            // Tổng số nhóm
            $totalNhom = Nhom::count();

            // Tổng số đề tài
            $totalDeTai = DeTai::count();

            return view('giangvien.dashboard', compact(
                'totalSinhVien',
                'totalGiangVien',
                'totalNhom',
                'totalDeTai'
            ));
        } catch (\Exception $e) {
            \Log::error('Error loading giangvien dashboard:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return view('giangvien.dashboard', [
                'totalSinhVien' => 0,
                'totalGiangVien' => 0,
                'totalNhom' => 0,
                'totalDeTai' => 0
            ]);
        }
    }
}
