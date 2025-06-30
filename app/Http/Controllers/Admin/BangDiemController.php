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

        // Gom nhóm bảng điểm theo sinh viên, tính trung bình tổng điểm
        $bangDiemBySinhVien = $bangDiems->groupBy('sinh_vien_id')->map(function($items) {
            $result = [
                'list' => $items,
                'diem_bao_cao_tb' => null,
                'tong_ket' => null,
                'diem_tong_ket' => null
            ];
            // Tính tổng điểm từng giảng viên (không cộng điểm báo cáo)
            $tongDiemArr = $items->map(function($bd) {
                return ($bd->diem_thuyet_trinh ?? 0) + ($bd->diem_demo ?? 0) + ($bd->diem_cau_hoi ?? 0) + ($bd->diem_cong ?? 0);
            });
            if ($tongDiemArr->count() > 0) {
                $result['tong_ket'] = round($tongDiemArr->avg(), 2);
            }
            // Trung bình điểm báo cáo
            $diemBaoCaoArr = $items->pluck('diem_bao_cao')->filter(function($v){ return $v !== null; });
            if ($diemBaoCaoArr->count() > 0) {
                $result['diem_bao_cao_tb'] = round($diemBaoCaoArr->avg(), 2);
            }
            // Điểm tổng kết = điểm trung bình báo cáo * 0.2 + điểm tổng trung bình * 0.8
            if ($result['diem_bao_cao_tb'] !== null && $result['tong_ket'] !== null) {
                $result['diem_tong_ket'] = round($result['diem_bao_cao_tb'] * 0.2 + $result['tong_ket'] * 0.8, 2);
            }
            return $result;
        });

        // Lấy danh sách đợt báo cáo và giảng viên cho filter
        $dotBaoCaos = DotBaoCao::with('hocKy')->get();
        $giangViens = TaiKhoan::where('vai_tro', 'giang_vien')->get();
        $sinhViens = SinhVien::all();

        return view('admin.bang-diem.index', compact('bangDiems', 'dotBaoCaos', 'giangViens', 'sinhViens', 'bangDiemBySinhVien'));
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

        // Thống kê tổng quan
        $thongKe = [
            'tong_so_diem' => $bangDiems->count(),
            'diem_trung_binh_bao_cao' => $bangDiems->avg('diem_bao_cao'),
            'diem_trung_binh_thuyet_trinh' => $bangDiems->avg('diem_thuyet_trinh'),
            'diem_trung_binh_demo' => $bangDiems->avg('diem_demo'),
            'diem_trung_binh_cau_hoi' => $bangDiems->avg('diem_cau_hoi'),
            'diem_trung_binh_cong' => $bangDiems->avg('diem_cong'),
            'diem_cao_nhat' => $bangDiems->max(function($item) {
                return $item->diem_bao_cao + $item->diem_thuyet_trinh + $item->diem_demo + $item->diem_cau_hoi + $item->diem_cong;
            }),
            'diem_thap_nhat' => $bangDiems->min(function($item) {
                return $item->diem_bao_cao + $item->diem_thuyet_trinh + $item->diem_demo + $item->diem_cau_hoi + $item->diem_cong;
            })
        ];

        // Thống kê theo giảng viên
        $thongKeTheoGiangVien = $bangDiems->groupBy('giang_vien_id')
            ->map(function($group) {
                return [
                    'giang_vien' => $group->first()->giangVien,
                    'so_luong' => $group->count(),
                    'diem_trung_binh' => $group->avg(function($item) {
                        return $item->diem_bao_cao + $item->diem_thuyet_trinh + $item->diem_demo + $item->diem_cau_hoi + $item->diem_cong;
                    })
                ];
            });

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

        $dotBaoCaos = DotBaoCao::with('hocKy')->get();

        return view('admin.bang-diem.thong-ke', compact('thongKe', 'thongKeTheoGiangVien', 'khoangDiem', 'dotBaoCaos', 'dotBaoCaoId'));
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