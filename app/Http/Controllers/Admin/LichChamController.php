<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LichCham;
use App\Models\HoiDong;
use App\Models\DotBaoCao;
use App\Models\Nhom;
use App\Models\DeTai;
use App\Models\PhanCongCham;
use App\Models\PhanCongVaiTro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class LichChamController extends Controller
{
    /**
     * Hiển thị danh sách lịch chấm
     */
    public function index()
    {
        $lichChams = LichCham::with(['hoiDong', 'dotBaoCao', 'nhom'])
            ->orderBy('lich_tao', 'desc')
            ->paginate(10);
            
        return view('admin.lich-cham.index', compact('lichChams'));
    }

    /**
     * Hiển thị form tạo lịch chấm mới
     */
    public function create()
    {
        $hoiDongs = HoiDong::all();
        $dotBaoCaos = DotBaoCao::all();
        $nhoms = Nhom::where('trang_thai', 'hoat_dong')
            ->whereHas('deTai') // Chỉ lấy các nhóm đã có đề tài
            ->select('id', 'ma_nhom', 'ten', 'giang_vien_id')
            ->with(['giangVien:id,ten', 'deTai:id,ten_de_tai'])
            ->get();
            
        return view('admin.lich-cham.create', compact('hoiDongs', 'dotBaoCaos', 'nhoms'));
    }

    /**
     * Lưu lịch chấm mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'hoi_dong_id' => 'required|exists:hoi_dongs,id',
            'dot_bao_cao_id' => 'required|exists:dot_bao_caos,id',
            'nhom_id' => 'required|exists:nhoms,id',
            'lich_tao' => 'required|date'
        ]);

        try {
            DB::beginTransaction();

            // Kiểm tra xung đột lịch chấm
            $xungDot = LichCham::where('hoi_dong_id', $request->hoi_dong_id)
                ->where('lich_tao', $request->lich_tao)
                ->exists();

            if ($xungDot) {
                throw new \Exception('Đã có lịch chấm cho hội đồng này vào thời gian này.');
            }

            // Lấy thông tin đề tài của nhóm
            $deTai = DeTai::where('nhom_id', $request->nhom_id)->first();
            if (!$deTai) {
                throw new \Exception('Nhóm chưa được gán đề tài.');
            }

            // Lấy thông tin phân công chấm
            $phanCongCham = PhanCongCham::where('de_tai_id', $deTai->id)->first();
            if (!$phanCongCham) {
                throw new \Exception('Đề tài chưa được phân công chấm.');
            }

            // Tạo lịch chấm với de_tai_id và phan_cong_cham_id
            $lichCham = new LichCham();
            $lichCham->hoi_dong_id = $request->hoi_dong_id;
            $lichCham->dot_bao_cao_id = $request->dot_bao_cao_id;
            $lichCham->nhom_id = $request->nhom_id;
            $lichCham->de_tai_id = $deTai->id;
            $lichCham->phan_cong_cham_id = $phanCongCham->id;
            $lichCham->lich_tao = $request->lich_tao;
            $lichCham->save();

            DB::commit();
            return redirect()->route('admin.lich-cham.index')
                ->with('success', 'Thêm lịch chấm thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Không thể thêm lịch chấm: ' . $e->getMessage()]);
        }
    }

    /**
     * Hiển thị form sửa lịch chấm
     */
    public function edit(LichCham $lichCham)
    {
        $hoiDongs = HoiDong::all();
        $dotBaoCaos = DotBaoCao::all();
        $nhoms = Nhom::where('trang_thai', 'hoat_dong')
            ->whereHas('deTai') // Chỉ lấy các nhóm đã có đề tài
            ->select('id', 'ma_nhom', 'ten', 'giang_vien_id')
            ->with(['giangVien:id,ten', 'deTai:id,ten_de_tai'])
            ->get();
            
        return view('admin.lich-cham.edit', compact('lichCham', 'hoiDongs', 'dotBaoCaos', 'nhoms'));
    }

    /**
     * Cập nhật lịch chấm
     */
    public function update(Request $request, LichCham $lichCham)
    {
        $request->validate([
            'hoi_dong_id' => 'required|exists:hoi_dongs,id',
            'dot_bao_cao_id' => 'required|exists:dot_bao_caos,id',
            'nhom_id' => 'required|exists:nhoms,id',
            'lich_tao' => 'required|date'
        ]);

        try {
            DB::beginTransaction();

            // Kiểm tra xung đột lịch chấm
            $xungDot = LichCham::where('hoi_dong_id', $request->hoi_dong_id)
                ->where('lich_tao', $request->lich_tao)
                ->where('id', '!=', $lichCham->id)
                ->exists();

            if ($xungDot) {
                throw new \Exception('Đã có lịch chấm cho hội đồng này vào thời gian này.');
            }

            // Lấy thông tin đề tài của nhóm
            $deTai = DeTai::where('nhom_id', $request->nhom_id)->first();
            if (!$deTai) {
                throw new \Exception('Nhóm chưa được gán đề tài.');
            }

            // Lấy thông tin phân công chấm
            $phanCongCham = PhanCongCham::where('de_tai_id', $deTai->id)->first();
            if (!$phanCongCham) {
                throw new \Exception('Đề tài chưa được phân công chấm.');
            }

            // Cập nhật lịch chấm với de_tai_id và phan_cong_cham_id
            $lichCham->hoi_dong_id = $request->hoi_dong_id;
            $lichCham->dot_bao_cao_id = $request->dot_bao_cao_id;
            $lichCham->nhom_id = $request->nhom_id;
            $lichCham->de_tai_id = $deTai->id;
            $lichCham->phan_cong_cham_id = $phanCongCham->id;
            $lichCham->lich_tao = $request->lich_tao;
            $lichCham->save();

            DB::commit();
            return redirect()->route('admin.lich-cham.index')
                ->with('success', 'Cập nhật lịch chấm thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Không thể cập nhật lịch chấm: ' . $e->getMessage()]);
        }
    }

    /**
     * Xóa lịch chấm
     */
    public function destroy(LichCham $lichCham)
    {
        try {
            DB::beginTransaction();

            // Kiểm tra xem lịch chấm đã diễn ra chưa
            if (Carbon::parse($lichCham->lich_tao)->isPast()) {
                throw new \Exception('Không thể xóa lịch chấm đã diễn ra.');
            }

            $lichCham->delete();

            DB::commit();
            return redirect()->route('admin.lich-cham.index')
                ->with('success', 'Xóa lịch chấm thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.lich-cham.index')
                ->with('error', 'Không thể xóa lịch chấm: ' . $e->getMessage());
        }
    }

    /**
     * Xuất PDF danh sách lịch chấm
     */
    public function exportPdf()
    {
        $lichChams = LichCham::with([
            'hoiDong.phong',
            'dotBaoCao', 
            'nhom.sinhViens.lop',
            'nhom.deTai',
            'nhom.giangVien',
            'phanCongCham.giangVienPhanBien'
        ])
        ->orderBy('lich_tao', 'desc')
        ->get()
        ->groupBy('hoi_dong_id');

        $groupedData = [];

        foreach ($lichChams as $hoiDongId => $lichChamsCollection) {
            $hoiDong = $lichChamsCollection->first()->hoiDong ?? null;
            $truongTieuBan = 'Chưa có trưởng tiểu ban';
            $thuKy = 'Chưa có thư ký';
            $lichTao = null;

            if ($hoiDong) {
                $phanCongVaiTros = PhanCongVaiTro::with(['taiKhoan', 'vaiTro'])
                    ->where('hoi_dong_id', $hoiDong->id)
                    ->get();

                foreach ($phanCongVaiTros as $phanCong) {
                    if ($phanCong->vaiTro->ten === 'Trưởng tiểu ban') {
                        $truongTieuBan = $phanCong->taiKhoan->ten ?? 'Chưa có trưởng tiểu ban';
                    } elseif ($phanCong->vaiTro->ten === 'Thư ký') {
                        $thuKy = $phanCong->taiKhoan->ten ?? 'Chưa có thư ký';
                    }
                }
            }

            // Lấy lich_tao mới nhất của hội đồng
            $latestLichCham = $lichChamsCollection->first();
            if ($latestLichCham) {
                $lichTao = Carbon::parse($latestLichCham->lich_tao)->format('H\hi \N\g\à\y d/m/Y');
            }

            $groupedData[] = [
                'hoiDong' => $hoiDong,
                'truongTieuBan' => $truongTieuBan,
                'thuKy' => $thuKy,
                'lichChams' => $lichChamsCollection,
                'lichTao' => $lichTao
            ];
        }

        $data = [
            'title' => 'DANH SÁCH LỊCH CHẤM',
            'groupedData' => $groupedData,
        ];

        $pdf = PDF::loadView('admin.lich-cham.pdf', $data);
        return $pdf->download('danh-sach-lich-cham.pdf');
    }
} 