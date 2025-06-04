<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use App\Models\DeTai;
use App\Models\DeTaiMau;
use App\Models\Nhom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DeTaiController extends Controller
{
    public function index()
    {
        $deTais = DeTai::with(['deTaiMau', 'nhom'])->get();
        $deTaiMaus = DeTaiMau::all();
        return view('giangvien.de-tai.index', compact('deTais', 'deTaiMaus'));
    }

    public function create()
    {
        $deTaiMaus = DeTaiMau::all();
        $nhoms = Nhom::all();
        return view('giangvien.de-tai.create', compact('deTaiMaus', 'nhoms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ma_de_tai' => 'required|string|max:255',
            'de_tai_mau_id' => 'required|exists:de_tai_maus,id',
            'mo_ta' => 'nullable|string',
            'ngay_bat_dau' => 'required|date',
            'ngay_ket_thuc' => 'required|date|after:ngay_bat_dau',
            'nhom_id' => 'nullable|exists:nhoms,id'
        ]);

        try {
            DeTai::create($request->all());
            return redirect()->route('giangvien.de-tai.index')->with('success', 'Thêm đề tài thành công');
        } catch (\Exception $e) {
            Log::error('Lỗi khi thêm đề tài: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi thêm đề tài');
        }
    }

    public function edit(DeTai $deTai)
    {
        $deTaiMaus = DeTaiMau::all();
        $nhoms = Nhom::all();
        return view('giangvien.de-tai.edit', compact('deTai', 'deTaiMaus', 'nhoms'));
    }

    public function update(Request $request, DeTai $deTai)
    {
        $request->validate([
            'ma_de_tai' => 'required|string|max:255',
            'de_tai_mau_id' => 'required|exists:de_tai_maus,id',
            'mo_ta' => 'nullable|string',
            'ngay_bat_dau' => 'required|date',
            'ngay_ket_thuc' => 'required|date|after:ngay_bat_dau',
            'nhom_id' => 'nullable|exists:nhoms,id'
        ]);

        try {
            $deTai->update($request->all());
            return redirect()->route('giangvien.de-tai.index')->with('success', 'Cập nhật đề tài thành công');
        } catch (\Exception $e) {
            Log::error('Lỗi khi cập nhật đề tài: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi cập nhật đề tài');
        }
    }

    public function destroy(DeTai $deTai)
    {
        try {
            $deTai->delete();
            return redirect()->route('giangvien.de-tai.index')->with('success', 'Xóa đề tài thành công');
        } catch (\Exception $e) {
            Log::error('Lỗi khi xóa đề tài: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi xóa đề tài');
        }
    }
} 