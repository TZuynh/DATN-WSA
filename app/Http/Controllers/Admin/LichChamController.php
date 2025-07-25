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
use Illuminate\Support\Facades\Log;

class LichChamController extends Controller
{
    /**
     * Hiển thị danh sách lịch chấm
     */
    public function index()
    {
        // Lấy và group theo hội đồng
        $lichChams = LichCham::with(['hoiDong', 'dotBaoCao', 'nhom', 'deTai'])
            ->orderBy('thu_tu', 'asc')
            ->get()
            ->groupBy('hoi_dong_id');

        return view('admin.lich-cham.index', compact('lichChams'));
    }

    /**
     * Hiển thị form tạo lịch chấm mới
     */
    public function create()
    {
        $hoiDongs = HoiDong::all();
        $dotBaoCaos = DotBaoCao::all();

        // Lấy danh sách de_tai_id đã được phân công chấm
        $deTaiIds = PhanCongCham::pluck('de_tai_id')->toArray();

        // Lấy danh sách nhóm đã có lịch chấm
        $nhomDaCoLichCham = \App\Models\LichCham::pluck('nhom_id')->toArray();

        // Lấy nhóm chưa có lịch chấm, có đề tài đã được phân công chấm
        $nhoms = Nhom::whereNotIn('id', $nhomDaCoLichCham)
            ->whereHas('deTai', function($query) use ($deTaiIds) {
                $query->whereIn('id', $deTaiIds);
            })
            ->get();

        if ($nhoms->isEmpty()) {
            return redirect()->route('admin.lich-cham.index')
                ->with('error', 'Không có nhóm nào phù hợp để thêm vào lịch bảo vệ. Vui lòng kiểm tra trạng thái đề tài.');
        }

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
            'de_tai_id' => 'required|exists:de_tais,id',
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
                throw new \Exception('Nhóm chưa được gán đề tài. Vui lòng kiểm tra lại thông tin nhóm.');
            }
            $deTaiId = $deTai->id;

            // Lấy thông tin phân công chấm
            $phanCongCham = PhanCongCham::where('de_tai_id', $deTaiId)->first();
            if (!$phanCongCham) {
                throw new \Exception('Đề tài "' . $deTai->ten_de_tai . '" chưa được phân công chấm. Vui lòng phân công chấm trước khi tạo lịch.');
            }

            // Cập nhật tất cả thu_tu hiện tại
            LichCham::query()->update(['thu_tu' => DB::raw('thu_tu + 1')]);

            // Tạo lịch chấm mới với thu_tu = 1
            $lichCham = new LichCham();
            $lichCham->hoi_dong_id = $request->hoi_dong_id;
            $lichCham->dot_bao_cao_id = $request->dot_bao_cao_id;
            $lichCham->nhom_id = $request->nhom_id;
            $lichCham->de_tai_id = $deTaiId;
            $lichCham->phan_cong_cham_id = $phanCongCham->id;
            $lichCham->lich_tao = $request->lich_tao;
            $lichCham->thu_tu = 1; // Luôn thêm mới ở vị trí đầu tiên
            $lichCham->save();

            // Cập nhật trạng thái đề tài thành Đang thực hiện (GVPB đồng ý)
            $deTai->trang_thai = DeTai::TRANG_THAI_DANG_THUC_HIEN_GVPB;
            $deTai->save();

            DB::commit();
            return redirect()->route('admin.lich-cham.index')
                ->with('success', 'Thêm lịch bảo vệ thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Không thể thêm lịch bảo vệ: ' . $e->getMessage()]);
        }
    }

    /**
     * Hiển thị form sửa lịch chấm
     */
    public function edit(LichCham $lichCham)
    {
        $hoiDongs = HoiDong::all();
        $dotBaoCaos = DotBaoCao::all();

        // Load relationship deTai
        $lichCham->load('deTai');

        // Lấy danh sách nhóm đã có lịch chấm (trừ nhóm hiện tại)
        $nhomDaCoLichCham = LichCham::where('id', '!=', $lichCham->id)
            ->pluck('nhom_id')
            ->toArray();

        // Lấy danh sách nhóm chưa có lịch chấm và nhóm hiện tại
        $nhoms = Nhom::where('trang_thai', 'hoat_dong')
            ->whereHas('deTais', function($query) {
                $query->whereHas('phanCongCham')
                    ->whereNotIn('trang_thai', [
                        DeTai::TRANG_THAI_CHO_DUYET,
                        DeTai::TRANG_THAI_KHONG_XAY_RA_GVHD,
                        DeTai::TRANG_THAI_KHONG_XAY_RA_GVPB
                    ]);
            })
            ->where(function($query) use ($lichCham, $nhomDaCoLichCham) {
                $query->where('id', $lichCham->nhom_id) // Thêm nhóm hiện tại
                      ->orWhereNotIn('id', $nhomDaCoLichCham); // Thêm các nhóm chưa có lịch chấm
            })
            ->select('id', 'ma_nhom', 'ten', 'giang_vien_id')
            ->with(['giangVien:id,ten', 'deTais' => function($query) {
                $query->whereHas('phanCongCham')
                    ->whereNotIn('trang_thai', [
                        DeTai::TRANG_THAI_CHO_DUYET,
                        DeTai::TRANG_THAI_KHONG_XAY_RA_GVHD,
                        DeTai::TRANG_THAI_KHONG_XAY_RA_GVPB
                    ]);
            }])
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

            // Nếu người dùng không đổi hội đồng và thời gian thì cho phép cập nhật
            if (
                $lichCham->hoi_dong_id != $request->hoi_dong_id ||
                $lichCham->lich_tao != $request->lich_tao
            ) {
                $xungDot = LichCham::where('hoi_dong_id', $request->hoi_dong_id)
                    ->where('lich_tao', $request->lich_tao)
                    ->where('id', '!=', $lichCham->id)
                    ->exists();

                if ($xungDot) {
                    throw new \Exception('Đã có lịch chấm cho hội đồng này vào thời gian này.');
                }
            }

            // Lấy thông tin đề tài của nhóm
            $deTai = DeTai::where('nhom_id', $request->nhom_id)->first();
            if (!$deTai) {
                throw new \Exception('Nhóm chưa được gán đề tài. Vui lòng kiểm tra lại thông tin nhóm.');
            }

            // Lấy thông tin phân công chấm
            $phanCongCham = PhanCongCham::where('de_tai_id', $deTai->id)->first();
            if (!$phanCongCham) {
                throw new \Exception('Đề tài "' . $deTai->ten_de_tai . '" chưa được phân công chấm. Vui lòng phân công chấm trước khi cập nhật lịch.');
            }

            // Kiểm tra nếu thay đổi đề tài
            $deTaiCu = $lichCham->deTai;
            if ($deTaiCu && $deTaiCu->id != $deTai->id) {
                // Nếu thay đổi đề tài, cần kiểm tra xem đề tài cũ có lịch chấm nào khác không
                $lichChamKhac = LichCham::where('de_tai_id', $deTaiCu->id)
                    ->where('id', '!=', $lichCham->id)
                    ->exists();

                if (!$lichChamKhac) {
                    // Nếu đề tài cũ không còn lịch chấm nào khác, có thể reset trạng thái về GVHD
                    $deTaiCu->trang_thai = DeTai::TRANG_THAI_DANG_THUC_HIEN_GVHD;
                    $deTaiCu->save();
                }
            }

            // Cập nhật lịch chấm với de_tai_id và phan_cong_cham_id
            $lichCham->hoi_dong_id = $request->hoi_dong_id;
            $lichCham->dot_bao_cao_id = $request->dot_bao_cao_id;
            $lichCham->nhom_id = $request->nhom_id;
            $lichCham->de_tai_id = $deTai->id;
            $lichCham->phan_cong_cham_id = $phanCongCham->id;
            $lichCham->lich_tao = $request->lich_tao;
            $lichCham->save();
            // Log sau khi save lichCham
            // Cập nhật trạng thái đề tài thành Đang thực hiện (GVPB đồng ý)
            $deTai->trang_thai = DeTai::TRANG_THAI_DANG_THUC_HIEN_GVPB;
            $deTai->save();

            DB::commit();
            return redirect()->route('admin.lich-cham.index')
                ->with('success', 'Cập nhật lịch chấm thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi cập nhật lịch chấm: ' . $e->getMessage());
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

            // // Kiểm tra xem lịch chấm đã diễn ra chưa
            // if (Carbon::parse($lichCham->lich_tao)->isPast()) {
            //     throw new \Exception('Không thể xóa lịch chấm đã diễn ra.');
            // }

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
            'nhom.deTais',
            'nhom.giangVien',
            'phanCongCham.giangVienPhanBien'
        ])
        ->orderBy('thu_tu', 'asc')
        ->get()
        ->groupBy('hoi_dong_id');

        $groupedData = [];

        foreach ($lichChams as $hoiDongId => $lichChamsCollection) {
            $hoiDong = $lichChamsCollection->first()->hoiDong ?? null;
            $truongTieuBan = 'Chưa có trưởng tiểu ban';
            $thuKy = 'Chưa có thư ký';
            $lichTao = null;
            $thoiGianBatDau = null;

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

                // Lấy thời gian bắt đầu của hội đồng
                $thoiGianBatDau = $hoiDong->thoi_gian_bat_dau ? Carbon::parse($hoiDong->thoi_gian_bat_dau)->format('H\hi \N\g\à\y d/m/Y') : null;
            }

            $latestLichCham = $lichChamsCollection->first();
            if ($latestLichCham) {
                $lichTao = Carbon::parse($latestLichCham->lich_tao)->format('H\hi \N\g\à\y d/m/Y');
            }

            $groupedData[] = [
                'hoiDong' => $hoiDong,
                'truongTieuBan' => $truongTieuBan,
                'thuKy' => $thuKy,
                'lichChams' => $lichChamsCollection->values(),
                'lichTao' => $lichTao,
                'thoiGianBatDau' => $thoiGianBatDau
            ];
        }

        // Lấy danh sách hội đồng theo thứ tự tạo (created_at tăng dần)
        $orderedHoiDongIds = \App\Models\HoiDong::orderBy('created_at', 'asc')->pluck('id')->toArray();

        // Sắp xếp lại $groupedData theo thứ tự hội đồng đã tạo
        usort($groupedData, function($a, $b) use ($orderedHoiDongIds) {
            $aIndex = array_search($a['hoiDong']->id, $orderedHoiDongIds);
            $bIndex = array_search($b['hoiDong']->id, $orderedHoiDongIds);
            return $aIndex <=> $bIndex;
        });

        $data = [
            'title' => 'DANH SÁCH LỊCH BẢO VỆ',
            'groupedData' => $groupedData,
        ];

        $pdf = PDF::loadView('admin.lich-cham.pdf', $data);
        return $pdf->download('danh-sach-lich-cham.pdf');
    }

    public function updateOrder(Request $request)
    {
        try {
            DB::beginTransaction();

            $orders = $request->input('orders');

            foreach ($orders as $order) {
                LichCham::where('id', $order['id'])
                        ->update(['thu_tu' => $order['new_order']]);
            }

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
