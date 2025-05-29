<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use App\Models\DangKyGiangVienHuongDan;
use App\Models\SinhVien;
use App\Models\TaiKhoan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class DashboardController extends Controller
{
    public function index()
    {
        try {
            // Total registrations
            $totalRegistrations = DangKyGiangVienHuongDan::count();

            // Registrations by status
            $approvedRegistrations = DangKyGiangVienHuongDan::where('trang_thai', 'da_duyet')->count();
            $pendingRegistrations = DangKyGiangVienHuongDan::where('trang_thai', 'cho_duyet')->count();
            $rejectedRegistrations = DangKyGiangVienHuongDan::where('trang_thai', 'tu_choi')->count();

            // Total students
            $totalStudents = SinhVien::count();

            // Students with registration vs without
            $studentsWithRegistration = SinhVien::whereHas('dangKyGiangVienHuongDan')->count();
            $studentsWithoutRegistration = $totalStudents - $studentsWithRegistration;

            // Lecturers (giảng viên) stats
            $totalLecturers = TaiKhoan::where('vai_tro', 'giang_vien')->count();

            // Registration by lecturer
            $registrationsByLecturer = DangKyGiangVienHuongDan::select('giang_vien_id', DB::raw('count(*) as count'))
                ->groupBy('giang_vien_id')
                ->with('giangVien:id,ten')
                ->orderBy('count', 'desc')
                ->limit(5)
                ->get();

            // Create student statistics
            $studentsByStatus = [
                (object) ['name' => 'Có đăng ký hướng dẫn', 'count' => $studentsWithRegistration],
                (object) ['name' => 'Chưa đăng ký hướng dẫn', 'count' => $studentsWithoutRegistration],
            ];

            // Latest registrations for activity feed
            $latestRegistrations = DangKyGiangVienHuongDan::with(['sinhVien', 'giangVien'])
                ->latest()
                ->take(5)
                ->get()
                ->map(function($item) {
                    return (object) [
                        'id' => $item->id,
                        'description' => "Sinh viên {$item->sinhVien->ten} đăng ký với giảng viên {$item->giangVien->ten}",
                        'status' => $item->trang_thai,
                        'created_at' => $item->created_at,
                    ];
                });

            // Calculate approved to rejected ratio (example)
            $approvedToRejectedRatio = ($rejectedRegistrations > 0)
                ? round($approvedRegistrations / $rejectedRegistrations, 2)
                : null;

            return view('giangvien.dashboard', compact(
                'totalRegistrations',
                'approvedRegistrations',
                'pendingRegistrations',
                'rejectedRegistrations',
                'totalStudents',
                'studentsWithRegistration',
                'studentsWithoutRegistration',
                'studentsByStatus',
                'totalLecturers',
                'registrationsByLecturer',
                'latestRegistrations',
                'approvedToRejectedRatio',
            ));
        } catch (\Exception $e) {
            \Log::error('Error loading giangvien dashboard:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Return view with empty/default data to avoid errors in blade
            return view('giangvien.dashboard', [
                'totalRegistrations' => 0,
                'approvedRegistrations' => 0,
                'pendingRegistrations' => 0,
                'rejectedRegistrations' => 0,
                'totalStudents' => 0,
                'studentsWithRegistration' => 0,
                'studentsWithoutRegistration' => 0,
                'studentsByStatus' => [],
                'totalLecturers' => 0,
                'registrationsByLecturer' => collect(),
                'latestRegistrations' => collect(),
                'approvedToRejectedRatio' => null,
            ]);
        }
    }
}
