<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HoiDong;
use App\Models\DotBaoCao;
use Illuminate\Http\Request;

class HoiDongController extends Controller
{
    public function index()
    {
        $hoiDongs = HoiDong::with('dotBaoCao')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('admin.hoi-dong.index', compact('hoiDongs'));
    }

    public function create()
    {
        $dotBaoCaos = DotBaoCao::all();
        return view('admin.hoi-dong.create', compact('dotBaoCaos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ma_hoi_dong' => 'required|unique:hoi_dongs,ma_hoi_dong',
            'ten' => 'required',
            'dot_bao_cao_id' => 'required|exists:dot_bao_caos,id'
        ]);

        HoiDong::create($request->all());

        return redirect()->route('admin.hoi-dong.index')
            ->with('success', 'Thêm hội đồng thành công.');
    }

    public function edit(HoiDong $hoiDong)
    {
        $dotBaoCaos = DotBaoCao::all();
        return view('admin.hoi-dong.edit', compact('hoiDong', 'dotBaoCaos'));
    }

    public function update(Request $request, HoiDong $hoiDong)
    {
        $request->validate([
            'ma_hoi_dong' => 'required|unique:hoi_dongs,ma_hoi_dong,' . $hoiDong->id,
            'ten' => 'required',
            'dot_bao_cao_id' => 'required|exists:dot_bao_caos,id'
        ]);

        $hoiDong->update($request->all());

        return redirect()->route('admin.hoi-dong.index')
            ->with('success', 'Cập nhật hội đồng thành công.');
    }

    public function destroy(HoiDong $hoiDong)
    {
        $hoiDong->delete();

        return redirect()->route('admin.hoi-dong.index')
            ->with('success', 'Xóa hội đồng thành công.');
    }
} 