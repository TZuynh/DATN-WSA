<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BaoCaoQuaTrinh;
use App\Models\Nhom;
use App\Models\DotBaoCao;
use App\Http\Requests\GiangVien\StoreBaoCaoQuaTrinhRequest;
use App\Http\Requests\GiangVien\UpdateBaoCaoQuaTrinhRequest;

class BaoCaoQuaTrinhController extends Controller
{
    public function index()
    {
        $baoCaoQuaTrinhs = BaoCaoQuaTrinh::with(['nhom', 'dotBaoCao'])->get();
        return view('giangvien.bao-cao-qua-trinh.index', compact('baoCaoQuaTrinhs'));
    }

    public function create()
    {
        $nhoms = Nhom::all();
        $dotBaoCaos = DotBaoCao::all();
        return view('giangvien.bao-cao-qua-trinh.create', compact('nhoms', 'dotBaoCaos'));
    }

    public function store(StoreBaoCaoQuaTrinhRequest $request)
    {
        $data = $request->validated();
        $data['ngay_bao_cao'] = $request->ngay_bao_cao;
        BaoCaoQuaTrinh::create($data);
        return redirect()->route('giangvien.bao-cao-qua-trinh.index')->with('success', 'Tạo báo cáo thành công!');
    }

    public function edit($id)
    {
        $baoCao = BaoCaoQuaTrinh::findOrFail($id);
        $nhoms = Nhom::all();
        $dotBaoCaos = DotBaoCao::all();
        return view('giangvien.bao-cao-qua-trinh.edit', compact('baoCao', 'nhoms', 'dotBaoCaos'));
    }

    public function update(UpdateBaoCaoQuaTrinhRequest $request, $id)
    {
        $baoCao = BaoCaoQuaTrinh::findOrFail($id);
        $data = $request->validated();
        $data['ngay_bao_cao'] = $request->ngay_bao_cao;
        $baoCao->update($data);
        return redirect()->route('giangvien.bao-cao-qua-trinh.index')->with('success', 'Cập nhật báo cáo thành công!');
    }

    public function destroy($id)
    {
        $baoCao = BaoCaoQuaTrinh::findOrFail($id);
        $baoCao->delete();
        return redirect()->route('giangvien.bao-cao-qua-trinh.index')->with('success', 'Xóa báo cáo thành công!');
    }

    public function show($id)
    {
        $baoCao = BaoCaoQuaTrinh::with(['nhom', 'dotBaoCao'])->findOrFail($id);
        return view('giangvien.bao-cao-qua-trinh.show', compact('baoCao'));
    }
} 