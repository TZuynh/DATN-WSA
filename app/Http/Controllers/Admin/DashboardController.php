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
        $data = [
            'totalTaiKhoan' => 0,
            'totalGiangVien' => 0,
            'totalHoiDong' => 0,
            'totalDotBaoCao' => 0
        ];

        try {
            $data['totalTaiKhoan'] = TaiKhoan::count() ?? 0;
            $data['totalGiangVien'] = TaiKhoan::where('vai_tro', 'giang_vien')->count() ?? 0;
            $data['totalHoiDong'] = HoiDong::count() ?? 0;
            $data['totalDotBaoCao'] = DotBaoCao::count() ?? 0;
        } catch (\Exception $e) {
            // Giữ giá trị mặc định là 0
        }

        return view('admin.dashboard', $data);
    }
} 