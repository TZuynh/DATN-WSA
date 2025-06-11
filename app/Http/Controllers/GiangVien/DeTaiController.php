<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use App\Models\DeTai;
use App\Models\Nhom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class DeTaiController extends Controller
{
    public function index()
    {
        $giangVienId = auth()->user()->id;
        $deTais = DeTai::with('nhom')
            ->where('giang_vien_id', $giangVienId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('giangvien.de-tai.index', compact('deTais'));
    }

    public function create()
    {
        $nhoms = Nhom::all();
        return view('giangvien.de-tai.create', compact('nhoms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ten_de_tai' => 'required|string',
            'mo_ta' => 'nullable|string',
            'y_kien_giang_vien' => 'nullable|string',
            'ngay_bat_dau' => 'required|date',
            'ngay_ket_thuc' => 'required|date|after:ngay_bat_dau',
            'nhom_id' => 'nullable|exists:nhoms,id'
        ]);

        try {
            // Tạo mảng dữ liệu với các trường cần thiết
            $data = [
                'ten_de_tai' => $request->ten_de_tai,
                'mo_ta' => $request->mo_ta,
                'y_kien_giang_vien' => $request->y_kien_giang_vien,
                'ngay_bat_dau' => $request->ngay_bat_dau,
                'ngay_ket_thuc' => $request->ngay_ket_thuc,
                'nhom_id' => $request->nhom_id,
                'giang_vien_id' => auth()->id(),
            ];

            DeTai::create($data);
            return redirect()->route('giangvien.de-tai.index')->with('success', 'Thêm đề tài thành công');
        } catch (\Exception $e) {
            Log::error('Error creating de tai: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi thêm đề tài');
        }
    }

    public function edit(DeTai $deTai)
    {
        if ($deTai->giang_vien_id !== auth()->id()) {
            return redirect()->route('giangvien.de-tai.index')
                ->with('error', 'Bạn không có quyền chỉnh sửa đề tài này.');
        }

        $nhoms = Nhom::all();
        return view('giangvien.de-tai.edit', compact('deTai', 'nhoms'));
    }

    public function update(Request $request, DeTai $deTai)
    {
        if ($deTai->giang_vien_id !== auth()->id()) {
            return redirect()->route('giangvien.de-tai.index')
                ->with('error', 'Bạn không có quyền cập nhật đề tài này.');
        }

        $request->validate([
            'ten_de_tai' => 'required|string',
            'mo_ta' => 'nullable|string',
            'y_kien_giang_vien' => 'nullable|string',
            'ngay_bat_dau' => 'required|date',
            'ngay_ket_thuc' => 'required|date|after:ngay_bat_dau',
            'nhom_id' => 'nullable|exists:nhoms,id',
            'trang_thai' => 'required|integer|in:0,1,2,3,4'
        ]);

        try {
            $deTai->update([
                'ten_de_tai' => $request->ten_de_tai,
                'mo_ta' => $request->mo_ta,
                'y_kien_giang_vien' => $request->y_kien_giang_vien,
                'ngay_bat_dau' => $request->ngay_bat_dau,
                'ngay_ket_thuc' => $request->ngay_ket_thuc,
                'nhom_id' => $request->nhom_id,
                'trang_thai' => $request->trang_thai
            ]);

            return redirect()->route('giangvien.de-tai.index')
                ->with('success', 'Cập nhật đề tài thành công');
        } catch (\Exception $e) {
            Log::error('Error updating de tai: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi cập nhật đề tài');
        }
    }

    public function destroy(DeTai $deTai)
    {
        if ($deTai->giang_vien_id !== auth()->id()) {
            return redirect()->route('giangvien.de-tai.index')
                ->with('error', 'Bạn không có quyền xóa đề tài này.');
        }

        try {
            $deTai->delete();
            return redirect()->route('giangvien.de-tai.index')
                ->with('success', 'Xóa đề tài thành công');
        } catch (\Exception $e) {
            Log::error('Error deleting de tai: ' . $e->getMessage());
            return redirect()->route('giangvien.de-tai.index')
                ->with('error', 'Có lỗi xảy ra khi xóa đề tài');
        }
    }

    public function updateTrangThai(Request $request, DeTai $deTai)
    {
        $request->validate([
            'trang_thai' => 'required|integer|min:0|max:4'
        ]);

        try {
            $deTai->update(['trang_thai' => $request->trang_thai]);
            return redirect()->back()->with('success', 'Cập nhật trạng thái đề tài thành công');
        } catch (\Exception $e) {
            Log::error('Lỗi khi cập nhật trạng thái đề tài: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi cập nhật trạng thái đề tài');
        }
    }

    public function exportPdfDetail(DeTai $deTai)
    {
        // Kiểm tra quyền xem chi tiết đề tài
        if ($deTai->giang_vien_id !== auth()->id()) {
            abort(403, 'Bạn không có quyền xem chi tiết đề tài này.');
        }

        // Load dữ liệu liên quan
        $deTai->load('nhom.sinhViens.lop', 'giangVien');

        // Tạo PDF
        $pdf = PDF::loadView('giangvien.de-tai.detail-pdf', compact('deTai'));
        
        // Cấu hình PDF
        $pdf->setPaper('a4', 'landscape');
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->setOption('defaultFont', 'DejaVu Sans');

        // Trả về file PDF để tải xuống
        return $pdf->download('de-tai-' . $deTai->id . '.pdf');
    }

    public function previewPdfDetail(DeTai $deTai)
    {
        // Kiểm tra quyền xem chi tiết đề tài
        if ($deTai->giang_vien_id !== auth()->id()) {
            abort(403, 'Bạn không có quyền xem chi tiết đề tài này.');
        }

        // Load dữ liệu liên quan
        $deTai->load('nhom.sinhViens.lop', 'giangVien');

        // Trả về view trực tiếp
        return view('giangvien.de-tai.detail-pdf', compact('deTai'));
    }
} 