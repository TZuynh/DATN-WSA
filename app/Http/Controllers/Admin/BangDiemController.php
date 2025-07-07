<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BangDiem;
use App\Models\SinhVien;
use App\Models\DotBaoCao;
use App\Models\PhanCongCham;
use App\Models\LichCham;
use App\Models\DeTai;
use App\Models\Nhom;
use App\Models\TaiKhoan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\BangDiemExport;
use Maatwebsite\Excel\Facades\Excel;

class BangDiemController extends Controller
{
    /**
     * Hiển thị danh sách tất cả bảng điểm
     */
    public function index(Request $request)
    {
        $query = BangDiem::with(['sinhVien', 'dotBaoCao', 'giangVien']);

        // Lọc theo đợt báo cáo
        if ($request->filled('dot_bao_cao_id')) {
            $query->where('dot_bao_cao_id', $request->dot_bao_cao_id);
        }

        // Lọc theo giảng viên
        if ($request->filled('giang_vien_id')) {
            $query->where('giang_vien_id', $request->giang_vien_id);
        }

        // Lọc theo sinh viên
        if ($request->filled('sinh_vien_id')) {
            $query->where('sinh_vien_id', $request->sinh_vien_id);
        }

        $bangDiems = $query->orderBy('created_at', 'desc')->get();

        $bangDiemBySinhVien = $bangDiems
            ->groupBy('sinh_vien_id')
            ->map(function($items) {
                // 1. Lọc bỏ các lần chấm tổng = 0
                $valid = $items->filter(function($bd) {
                    $tong =
                        ($bd->diem_thuyet_trinh ?? 0)
                    + ($bd->diem_demo          ?? 0)
                    + ($bd->diem_cau_hoi       ?? 0)
                    + ($bd->diem_cong          ?? 0);
                    return $tong > 0;
                });

                // 2. Trung bình điểm báo cáo
                $bcTB = $valid->avg('diem_bao_cao');

                // 3. Trung bình tổng 4 phần
                $tkTB = $valid->map(function($bd) {
                    return
                        ($bd->diem_thuyet_trinh ?? 0)
                    + ($bd->diem_demo          ?? 0)
                    + ($bd->diem_cau_hoi       ?? 0)
                    + ($bd->diem_cong          ?? 0);
                })->avg();

                // 4. Điểm tổng kết, áp trần 10
                $dtk = null;
                if (!is_null($bcTB) && !is_null($tkTB)) {
                    $dtk = min(round($bcTB * 0.2 + $tkTB * 0.8, 2), 10);
                }

                return [
                    'list'             => $items,
                    'diem_bao_cao_tb'  => is_null($bcTB) ? null : round($bcTB, 2),
                    'tong_ket'         => is_null($tkTB) ? null : round($tkTB, 2),
                    'diem_tong_ket'    => $dtk,
                ];
            })
            ->values();

        // dữ liệu filter dropdown như cũ
        $dotBaoCaos = DotBaoCao::with('hocKy')->get();
        $giangViens = TaiKhoan::where('vai_tro','giang_vien')->get();
        $sinhViens  = SinhVien::all();

        return view('admin.bang-diem.index', compact(
            'bangDiems','dotBaoCaos','giangViens','sinhViens','bangDiemBySinhVien'
        ));
    }


    /**
     * Hiển thị thống kê điểm
     */
    public function thongKe(Request $request)
    {
        $dotBaoCaoId = $request->get('dot_bao_cao_id');

        $query = BangDiem::with(['sinhVien', 'dotBaoCao', 'giangVien']);

        if ($dotBaoCaoId) {
            $query->where('dot_bao_cao_id', $dotBaoCaoId);
        }

        $bangDiems = $query->get();

        $valid = $bangDiems->filter(function($bd) {
            $total =
                ($bd->diem_thuyet_trinh ?? 0)
              + ($bd->diem_demo          ?? 0)
              + ($bd->diem_cau_hoi       ?? 0)
              + ($bd->diem_cong          ?? 0);
            return $total > 0;
        });

        // Thống kê tổng quan
        $thongKe = [
            'tong_so_diem'                => $bangDiems->count(),
            'diem_trung_binh_bao_cao'     => $valid->avg('diem_bao_cao'),
            'diem_trung_binh_thuyet_trinh'=> $valid->avg('diem_thuyet_trinh'),
            'diem_trung_binh_demo'        => $valid->avg('diem_demo'),
            'diem_trung_binh_cau_hoi'     => $valid->avg('diem_cau_hoi'),
            'diem_trung_binh_cong'        => $valid->avg('diem_cong'),
            'diem_cao_nhat' => $valid->max(function($i){
                return ($i->diem_bao_cao   ??0)
                      + ($i->diem_thuyet_trinh??0)
                      + ($i->diem_demo       ??0)
                      + ($i->diem_cau_hoi    ??0)
                      + ($i->diem_cong       ??0);
            }),
            'diem_thap_nhat' => $valid->min(function($i){
                return ($i->diem_bao_cao   ??0)
                      + ($i->diem_thuyet_trinh??0)
                      + ($i->diem_demo       ??0)
                      + ($i->diem_cau_hoi    ??0)
                      + ($i->diem_cong       ??0);
            }),
        ];

        // --- CHỖ CHỈNH SỬA: thêm filter loại bỏ giảng viên tổng = 0 ---
        $thongKeTheoGiangVien = $bangDiems->groupBy('giang_vien_id')
            ->map(function($group) {
                $total = $group->sum(function($item) {
                    return ($item->diem_bao_cao ?? 0)
                        + ($item->diem_thuyet_trinh ?? 0)
                        + ($item->diem_demo ?? 0)
                        + ($item->diem_cau_hoi ?? 0)
                        + ($item->diem_cong ?? 0);
                });
                // nếu tổng = 0 thì avg cũng = 0, nhưng ta sẽ lọc bên dưới
                return [
                    'giang_vien'     => $group->first()->giangVien,
                    'so_luong'       => $group->count(),
                    'diem_trung_binh'=> $total > 0
                        ? round($total / $group->count(), 2)
                        : 0,
                ];
            })
            ->filter(function($item) {
                // Chỉ giữ những giảng viên có diem_trung_binh > 0
                return $item['diem_trung_binh'] > 0;
            })
            ->values();
        // --------------------------------------------------------------

        // Nếu bạn cần tính trung bình chung qua các giảng viên (chỉ trên những người >0):
        $diemTrungBinhChung = $thongKeTheoGiangVien->avg('diem_trung_binh');

        // Thống kê theo khoảng điểm
        $khoangDiem = [
            '0-5'  => 0,
            '5-6'  => 0,
            '6-7'  => 0,
            '7-8'  => 0,
            '8-9'  => 0,
            '9-10' => 0
        ];

        foreach ($bangDiems as $bangDiem) {
            $tongDiem = ($bangDiem->diem_bao_cao ?? 0)
                    + ($bangDiem->diem_thuyet_trinh ?? 0)
                    + ($bangDiem->diem_demo ?? 0)
                    + ($bangDiem->diem_cau_hoi ?? 0)
                    + ($bangDiem->diem_cong ?? 0);

            if ($tongDiem < 5) {
                $khoangDiem['0-5']++;
            } elseif ($tongDiem < 6) {
                $khoangDiem['5-6']++;
            } elseif ($tongDiem < 7) {
                $khoangDiem['6-7']++;
            } elseif ($tongDiem < 8) {
                $khoangDiem['7-8']++;
            } elseif ($tongDiem < 9) {
                $khoangDiem['8-9']++;
            } else {
                $khoangDiem['9-10']++;
            }
        }

        $dotBaoCaos = DotBaoCao::with('hocKy')->get();

        return view('admin.bang-diem.thong-ke', compact(
            'thongKe',
            'thongKeTheoGiangVien',
            'diemTrungBinhChung', // nếu bạn muốn hiển thị
            'khoangDiem',
            'dotBaoCaos',
            'dotBaoCaoId'
        ));
    }

    /**
     * Hiển thị chi tiết điểm
     */
    public function show($id)
    {
        $bangDiem = BangDiem::with([
            'sinhVien.lop',
            'sinhVien.chiTietNhom.nhom.deTai',
            'dotBaoCao.lichChams.hoiDong',
            'giangVien'
        ])->findOrFail($id);

        // Thêm thông tin vai trò chấm
        $bangDiem->vai_tro_cham = '';
        $phanCongCham = PhanCongCham::whereHas('hoiDong.phanCongVaiTros', function($query) use ($bangDiem) {
            $query->where('tai_khoan_id', $bangDiem->giang_vien_id);
        })
        ->whereHas('deTai.nhom.sinhViens', function($query) use ($bangDiem) {
            $query->where('sinh_viens.id', $bangDiem->sinh_vien_id);
        })
        ->first();
        if ($phanCongCham && $phanCongCham->hoiDong) {
            $vaiTro = $phanCongCham->hoiDong->phanCongVaiTros->firstWhere('tai_khoan_id', $bangDiem->giang_vien_id);
            if ($vaiTro) {
                switch ($vaiTro->loai_giang_vien) {
                    case 'Giảng Viên Hướng Dẫn':
                        $bangDiem->vai_tro_cham = 'Hướng dẫn';
                        break;
                    case 'Giảng Viên Phản Biện':
                        $bangDiem->vai_tro_cham = 'Phản biện';
                        break;
                    case 'Giảng Viên Khác':
                        $bangDiem->vai_tro_cham = 'Giảng viên khác';
                        break;
                    default:
                        $bangDiem->vai_tro_cham = $vaiTro->loai_giang_vien;
                }
            }
        }

        return view('admin.bang-diem.show', compact('bangDiem'));
    }

    /**
     * Hiển thị form chỉnh sửa điểm
     */
    public function edit($id)
    {
        $bangDiem = BangDiem::with([
            'sinhVien.lop',
            'sinhVien.chiTietNhom.nhom.deTai',
            'dotBaoCao.lichChams.hoiDong'
        ])->findOrFail($id);

        return view('admin.bang-diem.edit', compact('bangDiem'));
    }

    /**
     * Cập nhật điểm
     */
    public function update(Request $request, $id)
    {
        $bangDiem = BangDiem::findOrFail($id);

        $request->validate([
            'diem_bao_cao' => 'required|numeric|min:0|max:10',
            'diem_thuyet_trinh' => 'required|numeric|min:0|max:10',
            'diem_demo' => 'required|numeric|min:0|max:10',
            'diem_cau_hoi' => 'required|numeric|min:0|max:10',
            'diem_cong' => 'nullable|numeric|min:0|max:2',
            'binh_luan' => 'nullable|string|max:1000'
        ]);

        try {
            $chiTietNhom = \App\Models\ChiTietNhom::where('sinh_vien_id', $bangDiem->sinh_vien_id)->first();
            $deTai = $chiTietNhom && $chiTietNhom->nhom ? $chiTietNhom->nhom->deTai : null;
            $bangDiem->update([
                'diem_bao_cao' => $request->diem_bao_cao,
                'diem_thuyet_trinh' => $request->diem_thuyet_trinh,
                'diem_demo' => $request->diem_demo,
                'diem_cau_hoi' => $request->diem_cau_hoi,
                'diem_cong' => $request->diem_cong ?? 0,
                'binh_luan' => $request->binh_luan,
                'de_tai_id' => $deTai ? $deTai->id : null
            ]);

            return redirect()->route('admin.bang-diem.index')
                ->with('success', 'Cập nhật điểm thành công.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi cập nhật điểm: ' . $e->getMessage());
        }
    }

    /**
     * Xóa điểm
     */
    public function destroy($id)
    {
        $bangDiem = BangDiem::findOrFail($id);

        try {
            $bangDiem->delete();
            return redirect()->route('admin.bang-diem.index')
                ->with('success', 'Xóa điểm thành công.');
        } catch (\Exception $e) {
            return redirect()->route('admin.bang-diem.index')
                ->with('error', 'Có lỗi xảy ra khi xóa điểm: ' . $e->getMessage());
        }
    }

    /**
     * Xuất Excel bảng điểm
     */
    public function export(Request $request)
    {
        $dotBaoCaoId = $request->get('dot_bao_cao_id');
        $filename = 'bang-diem-' . date('Y-m-d-H-i-s') . '.xlsx';

        return Excel::download(new BangDiemExport($dotBaoCaoId), $filename);
    }

    /**
     * Debug thống kê
     */
    public function debug()
    {
        $bangDiems = BangDiem::with(['sinhVien', 'dotBaoCao', 'giangVien'])->get();

        // Thống kê theo khoảng điểm
        $khoangDiem = [
            '0-5' => 0,
            '5-6' => 0,
            '6-7' => 0,
            '7-8' => 0,
            '8-9' => 0,
            '9-10' => 0
        ];

        foreach ($bangDiems as $bangDiem) {
            $tongDiem = ($bangDiem->diem_bao_cao ?? 0)
                + ($bangDiem->diem_thuyet_trinh ?? 0)
                + ($bangDiem->diem_demo ?? 0)
                + ($bangDiem->diem_cau_hoi ?? 0)
                + ($bangDiem->diem_cong ?? 0);

            if ($tongDiem < 5) {
                $khoangDiem['0-5']++;
            } elseif ($tongDiem < 6) {
                $khoangDiem['5-6']++;
            } elseif ($tongDiem < 7) {
                $khoangDiem['6-7']++;
            } elseif ($tongDiem < 8) {
                $khoangDiem['7-8']++;
            } elseif ($tongDiem < 9) {
                $khoangDiem['8-9']++;
            } else {
                $khoangDiem['9-10']++;
            }
        }

        return response()->json([
            'total_records' => $bangDiems->count(),
            'khoang_diem' => $khoangDiem,
            'sample_data' => $bangDiems->take(3)->map(function($item) {
                return [
                    'id' => $item->id,
                    'tong_diem' => $item->diem_bao_cao + $item->diem_thuyet_trinh + $item->diem_demo + $item->diem_cau_hoi + $item->diem_cong,
                    'diem_bao_cao' => $item->diem_bao_cao,
                    'diem_thuyet_trinh' => $item->diem_thuyet_trinh,
                    'diem_demo' => $item->diem_demo,
                    'diem_cau_hoi' => $item->diem_cau_hoi,
                    'diem_cong' => $item->diem_cong,
                ];
            })
        ]);
    }
}
