<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeTai;
use App\Models\DeTaiMau;
use App\Models\Nhom;
use App\Models\TaiKhoan;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\DeTaiImport;

class DeTaiController extends Controller
{
    public function index()
    {
        $deTais = DeTai::with(['deTaiMau', 'nhom', 'giangVien'])->latest()->paginate(10);
        $deTaiMaus = DeTaiMau::all();
        return view('admin.de-tai.index', compact('deTais', 'deTaiMaus'));
    }

    public function create()
    {
        $deTaiMaus = DeTaiMau::all();
        $nhoms = Nhom::all();
        $giangViens = TaiKhoan::where('vai_tro', 'giang_vien')->get();
        return view('admin.de-tai.create', compact('deTaiMaus', 'nhoms', 'giangViens'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ma_de_tai' => 'required|string|unique:de_tais',
            'de_tai_mau_id' => 'required|exists:de_tai_maus,id',
            'mo_ta' => 'nullable|string',
            'ngay_bat_dau' => 'nullable|date',
            'ngay_ket_thuc' => 'nullable|date|after_or_equal:ngay_bat_dau',
            'nhom_id' => 'nullable|exists:nhoms,id',
            'giang_vien_id' => 'nullable|exists:tai_khoans,id'
        ]);

        DeTai::create($request->all());

        return redirect()->route('admin.de-tai.index')
            ->with('success', 'Đề tài đã được tạo thành công.');
    }

    public function edit(DeTai $deTai)
    {
        $deTaiMaus = DeTaiMau::all();
        $nhoms = Nhom::all();
        $giangViens = TaiKhoan::where('vai_tro', 'giang_vien')->get();
        return view('admin.de-tai.edit', compact('deTai', 'deTaiMaus', 'nhoms', 'giangViens'));
    }

    public function update(Request $request, DeTai $deTai)
    {
        $request->validate([
            'ma_de_tai' => 'required|string|unique:de_tais,ma_de_tai,' . $deTai->id,
            'de_tai_mau_id' => 'required|exists:de_tai_maus,id',
            'mo_ta' => 'nullable|string',
            'ngay_bat_dau' => 'nullable|date',
            'ngay_ket_thuc' => 'nullable|date|after_or_equal:ngay_bat_dau',
            'nhom_id' => 'nullable|exists:nhoms,id',
            'giang_vien_id' => 'nullable|exists:tai_khoans,id'
        ]);

        $deTai->update($request->all());

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