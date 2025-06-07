<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeTai;
use App\Models\Nhom;
use App\Models\TaiKhoan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DeTaiController extends Controller
{
    public function index()
    {
        try {
            $deTais = DeTai::with(['nhom', 'giangVien'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);
            return view('admin.de-tai.index', compact('deTais'));
        } catch (\Exception $e) {
            Log::error('Error loading đề tài list: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi tải danh sách đề tài');
        }
    }

    public function create()
    {
        $nhoms = Nhom::all();
        $giangViens = TaiKhoan::where('vai_tro', 'giang_vien')->get();
        return view('admin.de-tai.create', compact('nhoms', 'giangViens'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ten_de_tai' => 'required|string',
            'mo_ta' => 'nullable|string',
            'y_kien_giang_vien' => 'nullable|string',
            'ngay_bat_dau' => 'required|date',
            'ngay_ket_thuc' => 'required|date|after:ngay_bat_dau',
            'nhom_id' => 'nullable|exists:nhoms,id',
            'giang_vien_id' => 'required|exists:tai_khoans,id'
        ]);

        try {
            $data = [
                'ten_de_tai' => $request->ten_de_tai,
                'mo_ta' => $request->mo_ta,
                'y_kien_giang_vien' => $request->y_kien_giang_vien,
                'ngay_bat_dau' => $request->ngay_bat_dau,
                'ngay_ket_thuc' => $request->ngay_ket_thuc,
                'nhom_id' => $request->nhom_id,
                'giang_vien_id' => $request->giang_vien_id
            ];

            DeTai::create($data);
            return redirect()->route('admin.de-tai.index')->with('success', 'Thêm đề tài thành công');
        } catch (\Exception $e) {
            Log::error('Error creating de tai: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi thêm đề tài');
        }
    }

    public function edit(DeTai $deTai)
    {
        $nhoms = Nhom::all();
        $giangViens = TaiKhoan::where('vai_tro', 'giang_vien')->get();
        return view('admin.de-tai.edit', compact('deTai', 'nhoms', 'giangViens'));
    }

    public function update(Request $request, DeTai $deTai)
    {
        $request->validate([
            'ten_de_tai' => 'required|string',
            'mo_ta' => 'nullable|string',
            'y_kien_giang_vien' => 'nullable|string',
            'ngay_bat_dau' => 'required|date',
            'ngay_ket_thuc' => 'required|date|after:ngay_bat_dau',
            'nhom_id' => 'nullable|exists:nhoms,id',
            'giang_vien_id' => 'required|exists:tai_khoans,id',
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
                'giang_vien_id' => $request->giang_vien_id,
                'trang_thai' => $request->trang_thai
            ]);

            return redirect()->route('admin.de-tai.index')
                ->with('success', 'Cập nhật đề tài thành công');
        } catch (\Exception $e) {
            Log::error('Error updating de tai: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi cập nhật đề tài');
        }
    }

    public function destroy(DeTai $deTai)
    {
        try {
            $deTai->delete();
            return redirect()->route('admin.de-tai.index')
                ->with('success', 'Xóa đề tài thành công');
        } catch (\Exception $e) {
            Log::error('Error deleting de tai: ' . $e->getMessage());
            return redirect()->route('admin.de-tai.index')
                ->with('error', 'Có lỗi xảy ra khi xóa đề tài');
        }
    }
}