<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DotBaoCao;
use App\Models\HoiDong;
use App\Models\ChiTietDeTaiBaoCao;
use App\Models\BaoCaoQuaTrinh;
use App\Models\BangDiem;
use App\Models\LichCham;
use App\Models\BienBanNhanXet;
use App\Models\PhanCongVaiTro;
use App\Models\HocKy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DotBaoCaoController extends Controller
{
    public function index()
    {
        $dotBaoCaos = DotBaoCao::with('hocKy')->orderBy('created_at', 'desc')
            ->paginate(10);
            
        // Lấy số lượng thực tế từ bảng cho từng đợt báo cáo
        foreach ($dotBaoCaos as $dotBaoCao) {
            $dotBaoCao->so_luong_hoi_dong_thuc_te = $dotBaoCao->hoiDongs()->count();
            $dotBaoCao->so_luong_de_tai_thuc_te = $dotBaoCao->deTais()->count();
            $dotBaoCao->so_luong_nhom_thuc_te = $dotBaoCao->deTais()->whereNotNull('nhom_id')->distinct('nhom_id')->count('nhom_id');
        }

        return view('admin.dot-bao-cao.index', compact('dotBaoCaos'));
    }

    public function create()
    {
        $hocKys = HocKy::all();
        return view('admin.dot-bao-cao.create', compact('hocKys'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nam_hoc' => 'required|integer|min:2000|max:2100',
            'hoc_ky_id' => 'required|exists:hoc_kys,id',
            'ngay_bat_dau' => 'required|date',
            'ngay_ket_thuc' => 'required|date|after:ngay_bat_dau'
        ]);

        // Kiểm tra năm học + học kỳ không trùng
        $exists = DotBaoCao::where('nam_hoc', $request->nam_hoc)
            ->where('hoc_ky_id', $request->hoc_ky_id)
            ->exists();
        if ($exists) {
            return back()->withErrors(['nam_hoc' => 'Năm học và học kỳ đã tồn tại.']);
        }

        $namBatDau = date('Y', strtotime($request->ngay_bat_dau));
        if ($namBatDau != $request->nam_hoc) {
            return back()->withErrors(['nam_hoc' => 'Năm học phải khớp với năm của ngày bắt đầu.']);
        }

        $dotBaoCao = DotBaoCao::create($request->all());
        $dotBaoCao->updateTrangThai();

        return redirect()->route('admin.dot-bao-cao.index')
            ->with('success', 'Thêm đợt báo cáo thành công.');
    }

    public function edit(DotBaoCao $dotBaoCao)
    {
        $hocKys = HocKy::all();
        return view('admin.dot-bao-cao.edit', compact('dotBaoCao', 'hocKys'));
    }

    public function update(Request $request, DotBaoCao $dotBaoCao)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'nam_hoc' => 'required|integer|min:2000|max:2100',
                'hoc_ky_id' => 'required|exists:hoc_kys,id',
                'ngay_bat_dau' => 'required|date',
                'ngay_ket_thuc' => 'required|date|after:ngay_bat_dau'
            ]);

            // Kiểm tra năm học + học kỳ không trùng (trừ bản ghi hiện tại)
            $exists = DotBaoCao::where('nam_hoc', $request->nam_hoc)
                ->where('hoc_ky_id', $request->hoc_ky_id)
                ->where('id', '!=', $dotBaoCao->id)
                ->exists();
            if ($exists) {
                return back()->withErrors(['nam_hoc' => 'Năm học và học kỳ đã tồn tại.']);
            }

            $namBatDau = date('Y', strtotime($request->ngay_bat_dau));
            if ($namBatDau != $request->nam_hoc) {
                return back()->withErrors(['nam_hoc' => 'Năm học phải khớp với năm của ngày bắt đầu.']);
            }

            $dotBaoCao->update([
                'nam_hoc' => $request->nam_hoc,
                'hoc_ky_id' => $request->hoc_ky_id,
                'ngay_bat_dau' => $request->ngay_bat_dau,
                'ngay_ket_thuc' => $request->ngay_ket_thuc,
                'mo_ta' => $request->mo_ta,
                'trang_thai' => $request->trang_thai
            ]);

            $dotBaoCao->updateTrangThai();

            DB::commit();

            return redirect()->route('admin.dot-bao-cao.index')
                ->with('success', 'Cập nhật đợt báo cáo thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Có lỗi xảy ra khi cập nhật đợt báo cáo: ' . $e->getMessage()]);
        }
    }

    public function destroy(DotBaoCao $dotBaoCao)
    {
        try {
            DB::beginTransaction();

            // Kiểm tra xem có hội đồng nào đang sử dụng đợt báo cáo này không
            $hoiDongCount = HoiDong::where('dot_bao_cao_id', $dotBaoCao->id)->count();
            if ($hoiDongCount > 0) {
                return redirect()->route('admin.dot-bao-cao.index')
                    ->with('error', 'Không thể xóa đợt báo cáo này vì đang có hội đồng sử dụng. Vui lòng xóa các hội đồng liên quan trước.');
            }

            // Xóa các bản ghi trực tiếp liên quan đến đợt báo cáo
            if (Schema::hasTable('chi_tiet_de_tai_bao_caos')) {
                ChiTietDeTaiBaoCao::where('dot_bao_cao_id', $dotBaoCao->id)->delete();
            }

            if (Schema::hasTable('bao_cao_qua_trinh')) {
                BaoCaoQuaTrinh::where('dot_bao_cao_id', $dotBaoCao->id)->delete();
            }

            if (Schema::hasTable('bang_diems')) {
                BangDiem::where('dot_bao_cao_id', $dotBaoCao->id)->delete();
            }

            if (Schema::hasTable('lich_chams')) {
                LichCham::where('dot_bao_cao_id', $dotBaoCao->id)->delete();
            }

            if (Schema::hasTable('bien_ban_nhan_xets')) {
                BienBanNhanXet::where('dot_bao_cao_id', $dotBaoCao->id)->delete();
            }

            // Xóa đợt báo cáo
            $dotBaoCao->delete();

            DB::commit();

            return redirect()->route('admin.dot-bao-cao.index')
                ->with('success', 'Xóa đợt báo cáo thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.dot-bao-cao.index')
                ->with('error', 'Có lỗi xảy ra khi xóa đợt báo cáo: ' . $e->getMessage());
        }
    }

    /**
     * Cập nhật trạng thái và thống kê của tất cả đợt báo cáo
     */
    public function updateStatus()
    {
        try {
            DB::beginTransaction();

            $dotBaoCaos = DotBaoCao::all();
            $now = now();

            foreach ($dotBaoCaos as $dotBaoCao) {
                // Cập nhật trạng thái
                $dotBaoCao->updateTrangThai();

                // Cập nhật thống kê
                $dotBaoCao->updateThongKe();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật trạng thái và thống kê thành công.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(DotBaoCao $dotBaoCao)
    {
        // Eager load các quan hệ cần thiết để view truy cập object Eloquent
        $dotBaoCao->load([
            'deTais.chiTietBaoCao.hoiDong',
            'deTais.nhom',
            'deTais.giangVien',
            'hoiDongs',
        ]);
        return view('admin.dot-bao-cao.show', compact('dotBaoCao'));
    }
} 