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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DotBaoCaoController extends Controller
{
    public function index()
    {
        $dotBaoCaos = DotBaoCao::orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('admin.dot-bao-cao.index', compact('dotBaoCaos'));
    }

    public function create()
    {
        return view('admin.dot-bao-cao.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nam_hoc' => 'required|integer|min:2000|max:2100'
        ]);

        DotBaoCao::create($request->all());

        return redirect()->route('admin.dot-bao-cao.index')
            ->with('success', 'Thêm đợt báo cáo thành công.');
    }

    public function edit(DotBaoCao $dotBaoCao)
    {
        return view('admin.dot-bao-cao.edit', compact('dotBaoCao'));
    }

    public function update(Request $request, DotBaoCao $dotBaoCao)
    {
        $request->validate([
            'nam_hoc' => 'required|integer|min:2000|max:2100'
        ]);

        $dotBaoCao->update($request->all());

        return redirect()->route('admin.dot-bao-cao.index')
            ->with('success', 'Cập nhật đợt báo cáo thành công.');
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

            if (Schema::hasTable('bao_cao_qua_trinhs')) {
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
} 