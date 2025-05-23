<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PhanCongVaiTro;
use App\Models\HoiDong;
use App\Models\TaiKhoan;
use App\Models\VaiTro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PhanCongHoiDongController extends Controller
{
    public function index()
    {
        $phanCongVaiTros = PhanCongVaiTro::with(['hoiDong', 'taiKhoan', 'vaiTro'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('admin.phan-cong-hoi-dong.index', compact('phanCongVaiTros'));
    }

    public function create()
    {
        $hoiDongs = HoiDong::all();
        $taiKhoans = TaiKhoan::where('vai_tro', 'giang_vien')->get();
        $vaiTros = VaiTro::all();
        
        return view('admin.phan-cong-hoi-dong.create', compact('hoiDongs', 'taiKhoans', 'vaiTros'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'hoi_dong_id' => 'required|exists:hoi_dongs,id',
            'tai_khoan_id' => 'required|exists:tai_khoans,id',
            'vai_tro_id' => 'required|exists:vai_tros,id'
        ]);

        // Kiểm tra xem giảng viên đã được phân công vào hội đồng này chưa
        $exists = PhanCongVaiTro::where('hoi_dong_id', $request->hoi_dong_id)
            ->where('tai_khoan_id', $request->tai_khoan_id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['tai_khoan_id' => 'Giảng viên này đã được phân công vào hội đồng.']);
        }

        PhanCongVaiTro::create($request->all());

        return redirect()->route('admin.phan-cong-hoi-dong.index')
            ->with('success', 'Phân công thành công.');
    }

    public function edit(PhanCongVaiTro $phanCongVaiTro)
    {
        $hoiDongs = HoiDong::all();
        $taiKhoans = TaiKhoan::where('vai_tro', 'giang_vien')->get();
        $vaiTros = VaiTro::all();
        
        return view('admin.phan-cong-hoi-dong.edit', compact('phanCongVaiTro', 'hoiDongs', 'taiKhoans', 'vaiTros'));
    }

    public function update(Request $request, PhanCongVaiTro $phanCongVaiTro)
    {
        $request->validate([
            'hoi_dong_id' => 'required|exists:hoi_dongs,id',
            'tai_khoan_id' => 'required|exists:tai_khoans,id',
            'vai_tro_id' => 'required|exists:vai_tros,id'
        ]);

        // Kiểm tra xem giảng viên đã được phân công vào hội đồng khác chưa
        $exists = PhanCongVaiTro::where('hoi_dong_id', $request->hoi_dong_id)
            ->where('tai_khoan_id', $request->tai_khoan_id)
            ->where('id', '!=', $phanCongVaiTro->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['tai_khoan_id' => 'Giảng viên này đã được phân công vào hội đồng.']);
        }

        $phanCongVaiTro->update($request->all());

        return redirect()->route('admin.phan-cong-hoi-dong.index')
            ->with('success', 'Cập nhật phân công thành công.');
    }

    public function destroy(PhanCongVaiTro $phanCongVaiTro)
    {
        $phanCongVaiTro->delete();

        return redirect()->route('admin.phan-cong-hoi-dong.index')
            ->with('success', 'Xóa phân công thành công.');
    }
} 