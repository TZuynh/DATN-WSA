<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeTai;
use App\Models\Nhom;
use App\Models\TaiKhoan;
use Illuminate\Http\Request;

class DeTaiController extends Controller
{
    public function index()
    {
        $deTais = DeTai::with(['nhom', 'giangVien'])->latest()->paginate(10);
        return view('admin.de-tai.index', compact('deTais'));
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
            'ma_de_tai' => 'required|string|unique:de_tais',
            'ten_de_tai' => 'required|string',
            'mo_ta' => 'nullable|string',
            'y_kien_giang_vien' => 'nullable|string',
            'ngay_bat_dau' => 'nullable|date',
            'ngay_ket_thuc' => 'nullable|date|after_or_equal:ngay_bat_dau',
            'nhom_id' => 'nullable|exists:nhoms,id',
            'giang_vien_id' => 'nullable|exists:tai_khoans,id',
            'trang_thai' => 'required|integer|in:0,1,2,3,4'
        ]);

        DeTai::create($request->all());

        return redirect()->route('admin.de-tai.index')
            ->with('success', 'Đề tài đã được tạo thành công.');
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
            'ma_de_tai' => 'required|string|unique:de_tais,ma_de_tai,' . $deTai->id,
            'ten_de_tai' => 'required|string',
            'mo_ta' => 'nullable|string',
            'y_kien_giang_vien' => 'nullable|string',
            'ngay_bat_dau' => 'nullable|date',
            'ngay_ket_thuc' => 'nullable|date|after_or_equal:ngay_bat_dau',
            'nhom_id' => 'nullable|exists:nhoms,id',
            'giang_vien_id' => 'nullable|exists:tai_khoans,id',
            'trang_thai' => 'required|integer|in:0,1,2,3,4'
        ]);

        $data = $request->all();
        
        // Xử lý ngày tháng
        $data['ngay_bat_dau'] = $data['ngay_bat_dau'] ? date('Y-m-d', strtotime($data['ngay_bat_dau'])) : null;
        $data['ngay_ket_thuc'] = $data['ngay_ket_thuc'] ? date('Y-m-d', strtotime($data['ngay_ket_thuc'])) : null;

        $deTai->update($data);

        return redirect()->route('admin.de-tai.index')
            ->with('success', 'Đề tài đã được cập nhật thành công.');
    }

    public function destroy(DeTai $deTai)
    {
        $deTai->delete();
        return redirect()->route('admin.de-tai.index')
            ->with('success', 'Đề tài đã được xóa thành công.');
    }
}