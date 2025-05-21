<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HoiDong;
use App\Models\DotBaoCao;
use App\Models\PhanCongVaiTro;
use App\Models\ChiTietDeTaiBaoCao;
use App\Models\LichCham;
use App\Models\BienBanNhanXet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HoiDongController extends Controller
{
    /**
     * Hiển thị danh sách hội đồng
     */
    public function index()
    {
        $hoiDongs = HoiDong::with('dotBaoCao')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('admin.hoi-dong.index', compact('hoiDongs'));
    }

    /**
     * Lưu hội đồng mới
     */
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

    /**
     * Hiển thị form sửa hội đồng
     */
    public function edit(HoiDong $hoiDong)
    {
        $dotBaoCaos = DotBaoCao::all();
        return view('admin.hoi-dong.edit', compact('hoiDong', 'dotBaoCaos'));
    }

    /**
     * Cập nhật hội đồng
     */
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

    /**
     * Xóa hội đồng
     */
    public function destroy(HoiDong $hoiDong)
    {
        try {
            DB::beginTransaction();

            // Xóa các phân công vai trò liên quan
            PhanCongVaiTro::where('hoi_dong_id', $hoiDong->id)->delete();

            // Xóa các chi tiết đề tài báo cáo liên quan
            ChiTietDeTaiBaoCao::where('hoi_dong_id', $hoiDong->id)->delete();

            // Xóa các lịch chấm liên quan
            LichCham::where('hoi_dong_id', $hoiDong->id)->delete();

            // Xóa các biên bản nhận xét liên quan
            BienBanNhanXet::where('hoi_dong_id', $hoiDong->id)->delete();

            // Xóa hội đồng
            $hoiDong->delete();

            DB::commit();

            return redirect()->route('admin.hoi-dong.index')
                ->with('success', 'Xóa hội đồng thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.hoi-dong.index')
                ->with('error', 'Không thể xóa hội đồng này vì có dữ liệu liên quan.');
        }
    }
} 