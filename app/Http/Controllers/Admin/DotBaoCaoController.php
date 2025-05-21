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

            // Lấy danh sách hội đồng cần xóa
            $hoiDongIds = HoiDong::where('dot_bao_cao_id', $dotBaoCao->id)->pluck('id');

            // Xóa các phân công vai trò liên quan đến hội đồng
            PhanCongVaiTro::whereIn('hoi_dong_id', $hoiDongIds)->delete();

            // Xóa các bản ghi liên quan
            ChiTietDeTaiBaoCao::where('dot_bao_cao_id', $dotBaoCao->id)->delete();
            BaoCaoQuaTrinh::where('dot_bao_cao_id', $dotBaoCao->id)->delete();
            BangDiem::where('dot_bao_cao_id', $dotBaoCao->id)->delete();
            LichCham::where('dot_bao_cao_id', $dotBaoCao->id)->delete();
            BienBanNhanXet::where('dot_bao_cao_id', $dotBaoCao->id)->delete();
            
            // Xóa các hội đồng liên quan
            HoiDong::where('dot_bao_cao_id', $dotBaoCao->id)->delete();

            // Xóa đợt báo cáo
            $dotBaoCao->delete();

            DB::commit();

            return redirect()->route('admin.dot-bao-cao.index')
                ->with('success', 'Xóa đợt báo cáo thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.dot-bao-cao.index')
                ->with('error', 'Không thể xóa đợt báo cáo này vì có dữ liệu liên quan.');
        }
    }
} 