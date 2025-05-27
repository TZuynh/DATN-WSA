<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            Log::info('Loading giangvien dashboard');
            
            // TODO: Thêm logic lấy dữ liệu cho dashboard giảng viên
            
            return view('giangvien.dashboard');
        } catch (\Exception $e) {
            Log::error('Error loading giangvien dashboard:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return view('giangvien.dashboard');
        }
    }
} 