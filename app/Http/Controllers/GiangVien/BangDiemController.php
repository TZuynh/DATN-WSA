<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use App\Models\BangDiem;
use App\Models\SinhVien;
use App\Models\DotBaoCao;
use App\Models\PhanCongCham;
use App\Models\LichCham;
use App\Models\DeTai;
use App\Models\Nhom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BangDiemController extends Controller
{
    /**
     * Hiển thị danh sách sinh viên cần chấm điểm
     */
    public function index()
    {
        $giangVienId = (int)Auth::id();

        // Lấy các phân công chấm có vai trò của giảng viên hiện tại trong hội đồng
        $phanCongChams = PhanCongCham::whereHas('hoiDong.phanCongVaiTros', function ($query) use ($giangVienId) {
            $query->where('tai_khoan_id', $giangVienId);
        })
            ->with(['deTai.nhom.sinhViens', 'deTai.lichCham', 'hoiDong.phanCongVaiTros'])
            ->get();

        // Lọc phân công hợp lệ (có đề tài + nhóm + sinh viên + lịch chấm)
        $phanCongChamsFiltered = $phanCongChams->filter(function ($phanCong) {
            return $phanCong->deTai
                && $phanCong->deTai->nhom
                && $phanCong->deTai->nhom->sinhViens->count() > 0
                && $phanCong->deTai->lichCham;
        });

        // Gán vai trò chấm
        $phanCongChamsFiltered = $phanCongChamsFiltered->map(function ($phanCong) use ($giangVienId) {
            $phanCong->vai_tro_cham = 'N/A';

            if ((int)$phanCong->giang_vien_phan_bien_id === $giangVienId) {
                $phanCong->vai_tro_cham = 'Phản biện';
            } elseif ((int)$phanCong->giang_vien_khac_id === $giangVienId) {
                $phanCong->vai_tro_cham = 'Giảng viên khác';
            } elseif ((int)$phanCong->giang_vien_huong_dan_id === $giangVienId) {
                $phanCong->vai_tro_cham = 'Hướng dẫn';
            }

            return $phanCong;
        });

        // Lấy danh sách bảng điểm đã chấm
        $bangDiems = BangDiem::where('giang_vien_id', $giangVienId)
            ->with(['sinhVien', 'dotBaoCao'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Gán vai trò chấm cho bảng điểm
        $bangDiems = $bangDiems->map(function ($bangDiem) use ($giangVienId) {
            $bangDiem->vai_tro_cham = 'N/A';

            $phanCongCham = PhanCongCham::where(function ($query) use ($giangVienId) {
                $query->where('giang_vien_phan_bien_id', $giangVienId)
                    ->orWhere('giang_vien_khac_id', $giangVienId)
                    ->orWhere('giang_vien_huong_dan_id', $giangVienId);
            })
                ->whereHas('deTai.nhom.sinhViens', function ($query) use ($bangDiem) {
                    $query->where('sinh_viens.id', $bangDiem->sinh_vien_id);
                })
                ->first();

            if ($phanCongCham) {
                if ((int)$phanCongCham->giang_vien_phan_bien_id === $giangVienId) {
                    $bangDiem->vai_tro_cham = 'Phản biện';
                } elseif ((int)$phanCongCham->giang_vien_khac_id === $giangVienId) {
                    $bangDiem->vai_tro_cham = 'Giảng viên khác';
                } elseif ((int)$phanCongCham->giang_vien_huong_dan_id === $giangVienId) {
                    $bangDiem->vai_tro_cham = 'Hướng dẫn';
                }
            }

            return $bangDiem;
        });

        return view('giangvien.bang-diem.index', compact('phanCongChamsFiltered', 'bangDiems'));
    }

    /**
     * Hiển thị form chấm điểm cho sinh viên
     */
    public function create($sinhVienId, $dotBaoCaoId)
    {
        $sinhVien = SinhVien::with(['lop', 'chiTietNhom.nhom.deTai'])->findOrFail($sinhVienId);
        $dotBaoCao = DotBaoCao::with(['lichChams.hoiDong'])->findOrFail($dotBaoCaoId);
        $giangVienId = Auth::id();

        // Kiểm tra giảng viên có quyền chấm điểm không và đề tài đã có trong lịch chấm
        $coQuyenCham = PhanCongCham::where(function($query) use ($giangVienId) {
            $query->where('giang_vien_phan_bien_id', $giangVienId)
                  ->orWhere('giang_vien_khac_id', $giangVienId);
        })
        ->whereHas('deTai.nhom.sinhViens', function($query) use ($sinhVienId) {
            $query->where('sinh_viens.id', $sinhVienId);
        })
        ->whereHas('deTai.lichCham')
        ->exists();

        if (!$coQuyenCham) {
            return redirect()->route('giangvien.bang-diem.index')
                ->with('error', 'Bạn không có quyền chấm điểm cho sinh viên này hoặc đề tài chưa có trong lịch chấm.');
        }

        // Kiểm tra đã chấm điểm chưa
        $daChamDiem = BangDiem::where('giang_vien_id', $giangVienId)
            ->where('sinh_vien_id', $sinhVienId)
            ->where('dot_bao_cao_id', $dotBaoCaoId)
            ->exists();

        if ($daChamDiem) {
            return redirect()->route('giangvien.bang-diem.index')
                ->with('error', 'Bạn đã chấm điểm cho sinh viên này trong đợt báo cáo này.');
        }

        return view('giangvien.bang-diem.create', compact('sinhVien', 'dotBaoCao'));
    }

    /**
     * Lưu điểm chấm
     */
    public function store(Request $request, $sinhVienId, $dotBaoCaoId)
    {
        $request->validate([
            'diem_bao_cao' => 'required|numeric|min:0|max:10',
            'diem_thuyet_trinh' => 'required|numeric|min:0|max:10',
            'diem_demo' => 'required|numeric|min:0|max:10',
            'diem_cau_hoi' => 'required|numeric|min:0|max:10',
            'diem_cong' => 'nullable|numeric|min:0|max:2',
            'binh_luan' => 'nullable|string|max:1000'
        ]);

        $giangVienId = Auth::id();

        // Kiểm tra quyền chấm điểm và đề tài đã có trong lịch chấm
        $coQuyenCham = PhanCongCham::where(function($query) use ($giangVienId) {
            $query->where('giang_vien_phan_bien_id', $giangVienId)
                  ->orWhere('giang_vien_khac_id', $giangVienId);
        })
        ->whereHas('deTai.nhom.sinhViens', function($query) use ($sinhVienId) {
            $query->where('sinh_viens.id', $sinhVienId);
        })
        ->whereHas('deTai.lichCham')
        ->exists();

        if (!$coQuyenCham) {
            return redirect()->route('giangvien.bang-diem.index')
                ->with('error', 'Bạn không có quyền chấm điểm cho sinh viên này hoặc đề tài chưa có trong lịch chấm.');
        }

        // Kiểm tra đã chấm điểm chưa
        $daChamDiem = BangDiem::where('giang_vien_id', $giangVienId)
            ->where('sinh_vien_id', $sinhVienId)
            ->where('dot_bao_cao_id', $dotBaoCaoId)
            ->exists();

        if ($daChamDiem) {
            return redirect()->route('giangvien.bang-diem.index')
                ->with('error', 'Bạn đã chấm điểm cho sinh viên này trong đợt báo cáo này.');
        }

        try {
            BangDiem::create([
                'giang_vien_id' => $giangVienId,
                'sinh_vien_id' => $sinhVienId,
                'dot_bao_cao_id' => $dotBaoCaoId,
                'diem_bao_cao' => $request->diem_bao_cao,
                'diem_thuyet_trinh' => $request->diem_thuyet_trinh,
                'diem_demo' => $request->diem_demo,
                'diem_cau_hoi' => $request->diem_cau_hoi,
                'diem_cong' => $request->diem_cong ?? 0,
                'binh_luan' => $request->binh_luan
            ]);

            return redirect()->route('giangvien.bang-diem.index')
                ->with('success', 'Chấm điểm thành công.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi chấm điểm: ' . $e->getMessage());
        }
    }

    /**
     * Hiển thị chi tiết điểm đã chấm
     */
    public function show($id)
    {
        $bangDiem = BangDiem::with([
            'sinhVien.lop',
            'sinhVien.chiTietNhom.nhom.deTai',
            'dotBaoCao.lichChams.hoiDong',
            'giangVien'
        ])->findOrFail($id);

        // Kiểm tra quyền xem
        if ($bangDiem->giang_vien_id !== Auth::id()) {
            return redirect()->route('giangvien.bang-diem.index')
                ->with('error', 'Bạn không có quyền xem điểm này.');
        }

        // Thêm thông tin vai trò chấm
        $bangDiem->vai_tro_cham = '';
        $phanCongCham = PhanCongCham::where(function($query) use ($bangDiem) {
            $query->where('giang_vien_phan_bien_id', $bangDiem->giang_vien_id)
                  ->orWhere('giang_vien_khac_id', $bangDiem->giang_vien_id)
                  ->orWhere('giang_vien_huong_dan_id', $bangDiem->giang_vien_id);
        })
        ->whereHas('deTai.nhom.sinhViens', function($query) use ($bangDiem) {
            $query->where('sinh_viens.id', $bangDiem->sinh_vien_id);
        })
        ->first();

        if ($phanCongCham) {
            if ($phanCongCham->giang_vien_phan_bien_id == $bangDiem->giang_vien_id) {
                $bangDiem->vai_tro_cham = 'Phản biện';
            } elseif ($phanCongCham->giang_vien_khac_id == $bangDiem->giang_vien_id) {
                $bangDiem->vai_tro_cham = 'Giảng viên khác';
            } elseif ($phanCongCham->giang_vien_huong_dan_id == $bangDiem->giang_vien_id) {
                $bangDiem->vai_tro_cham = 'Hướng dẫn';
            }
        }

        return view('giangvien.bang-diem.show', compact('bangDiem'));
    }

    /**
     * Hiển thị form chỉnh sửa điểm
     */
    public function edit($id)
    {
        $bangDiem = BangDiem::with([
            'sinhVien.lop',
            'sinhVien.chiTietNhom.nhom.deTai',
            'dotBaoCao.hoiDong',
            'dotBaoCao.lichChams.hoiDong'
        ])->findOrFail($id);

        // Kiểm tra quyền chỉnh sửa
        if ($bangDiem->giang_vien_id !== Auth::id()) {
            return redirect()->route('giangvien.bang-diem.index')
                ->with('error', 'Bạn không có quyền chỉnh sửa điểm này.');
        }

        return view('giangvien.bang-diem.edit', compact('bangDiem'));
    }

    /**
     * Cập nhật điểm
     */
    public function update(Request $request, $id)
    {
        $bangDiem = BangDiem::findOrFail($id);

        // Kiểm tra quyền chỉnh sửa
        if ($bangDiem->giang_vien_id !== Auth::id()) {
            return redirect()->route('giangvien.bang-diem.index')
                ->with('error', 'Bạn không có quyền chỉnh sửa điểm này.');
        }

        $request->validate([
            'diem_bao_cao' => 'required|numeric|min:0|max:10',
            'diem_thuyet_trinh' => 'required|numeric|min:0|max:10',
            'diem_demo' => 'required|numeric|min:0|max:10',
            'diem_cau_hoi' => 'required|numeric|min:0|max:10',
            'diem_cong' => 'nullable|numeric|min:0|max:2',
            'binh_luan' => 'nullable|string|max:1000'
        ]);

        try {
            $bangDiem->update([
                'diem_bao_cao' => $request->diem_bao_cao,
                'diem_thuyet_trinh' => $request->diem_thuyet_trinh,
                'diem_demo' => $request->diem_demo,
                'diem_cau_hoi' => $request->diem_cau_hoi,
                'diem_cong' => $request->diem_cong ?? 0,
                'binh_luan' => $request->binh_luan
            ]);

            return redirect()->route('giangvien.bang-diem.index')
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

        // Kiểm tra quyền xóa
        if ($bangDiem->giang_vien_id !== Auth::id()) {
            return redirect()->route('giangvien.bang-diem.index')
                ->with('error', 'Bạn không có quyền xóa điểm này.');
        }

        try {
            $bangDiem->delete();
            return redirect()->route('giangvien.bang-diem.index')
                ->with('success', 'Xóa điểm thành công.');
        } catch (\Exception $e) {
            return redirect()->route('giangvien.bang-diem.index')
                ->with('error', 'Có lỗi xảy ra khi xóa điểm: ' . $e->getMessage());
        }
    }

    /**
     * Route debug tạm thời để kiểm tra dữ liệu
     */
    public function debug()
    {
        $giangVienId = Auth::id();

        // Kiểm tra chi tiết từng bước
        $debugData = [
            'giang_vien_id' => $giangVienId,
            'phan_cong_chams_total' => PhanCongCham::count(),
            'phan_cong_chams_for_giang_vien' => PhanCongCham::where(function($query) use ($giangVienId) {
                $query->where('giang_vien_phan_bien_id', $giangVienId)
                      ->orWhere('giang_vien_khac_id', $giangVienId);
            })->count(),
            'lich_chams_total' => LichCham::count(),
            'de_tais_total' => DeTai::count(),
            'nhoms_total' => Nhom::count(),
            'sinh_viens_total' => SinhVien::count(),
            'dot_bao_caos_total' => DotBaoCao::count(),
            'bang_diems_total' => BangDiem::count(),
        ];

        // Kiểm tra chi tiết phân công chấm
        $phanCongChamQuery = PhanCongCham::where(function($query) use ($giangVienId) {
            $query->where('giang_vien_phan_bien_id', $giangVienId)
                  ->orWhere('giang_vien_khac_id', $giangVienId);
        });

        $debugData['phan_cong_cham_basic'] = $phanCongChamQuery->count();

        // Kiểm tra đề tài có lịch chấm
        $phanCongChamWithLichCham = $phanCongChamQuery->whereHas('deTai.lichCham');
        $debugData['phan_cong_cham_with_lich_cham'] = $phanCongChamWithLichCham->count();

        // Kiểm tra đề tài có sinh viên
        $phanCongChamWithSinhVien = $phanCongChamWithLichCham->whereHas('deTai.nhom.sinhViens');
        $debugData['phan_cong_cham_with_sinh_vien'] = $phanCongChamWithSinhVien->count();

        // Kiểm tra chi tiết từng phân công chấm
        $phanCongChams = $phanCongChamQuery->with(['deTai.nhom.sinhViens', 'deTai.lichCham'])->get();
        $debugData['phan_cong_cham_details'] = [];

        foreach ($phanCongChams as $phanCong) {
            $debugData['phan_cong_cham_details'][] = [
                'id' => $phanCong->id,
                'de_tai_id' => $phanCong->de_tai_id,
                'de_tai_ten' => $phanCong->deTai ? $phanCong->deTai->ten_de_tai : 'N/A',
                'nhom_id' => $phanCong->deTai && $phanCong->deTai->nhom ? $phanCong->deTai->nhom->id : 'N/A',
                'nhom_ten' => $phanCong->deTai && $phanCong->deTai->nhom ? $phanCong->deTai->nhom->ten : 'N/A',
                'sinh_viens_count' => $phanCong->deTai && $phanCong->deTai->nhom ? $phanCong->deTai->nhom->sinhViens->count() : 0,
                'lich_cham_id' => $phanCong->deTai && $phanCong->deTai->lichCham ? $phanCong->deTai->lichCham->id : 'N/A',
                'lich_cham_date' => $phanCong->deTai && $phanCong->deTai->lichCham ? $phanCong->deTai->lichCham->lich_tao : 'N/A',
            ];
        }

        return response()->json($debugData);
    }

    /**
     * Debug thông tin chi tiết bảng điểm
     */
    public function debugBangDiem($id)
    {
        $bangDiem = BangDiem::with([
            'sinhVien.lop',
            'sinhVien.chiTietNhom.nhom.deTai',
            'dotBaoCao.hoiDong',
            'dotBaoCao.lichChams.hoiDong'
        ])->findOrFail($id);

        $debugInfo = [
            'bang_diem_id' => $bangDiem->id,
            'sinh_vien' => [
                'id' => $bangDiem->sinhVien->id,
                'ten' => $bangDiem->sinhVien->ten,
                'mssv' => $bangDiem->sinhVien->mssv,
                'lop' => $bangDiem->sinhVien->lop ? $bangDiem->sinhVien->lop->ten_lop : 'N/A',
                'chi_tiet_nhom' => $bangDiem->sinhVien->chiTietNhom ? [
                    'id' => $bangDiem->sinhVien->chiTietNhom->id,
                    'nhom_id' => $bangDiem->sinhVien->chiTietNhom->nhom_id,
                    'nhom' => $bangDiem->sinhVien->chiTietNhom->nhom ? [
                        'id' => $bangDiem->sinhVien->chiTietNhom->nhom->id,
                        'ten_nhom' => $bangDiem->sinhVien->chiTietNhom->nhom->ten,
                        'de_tai' => $bangDiem->sinhVien->chiTietNhom->nhom->deTai ? [
                            'id' => $bangDiem->sinhVien->chiTietNhom->nhom->deTai->id,
                            'ten_de_tai' => $bangDiem->sinhVien->chiTietNhom->nhom->deTai->ten_de_tai
                        ] : 'N/A'
                    ] : 'N/A'
                ] : 'N/A'
            ],
            'dot_bao_cao' => [
                'id' => $bangDiem->dotBaoCao->id,
                'ten_dot_bao_cao' => $bangDiem->dotBaoCao->ten_dot_bao_cao,
                'nam_hoc' => $bangDiem->dotBaoCao->nam_hoc,
                'hoi_dong_direct' => $bangDiem->dotBaoCao->hoiDong ? [
                    'id' => $bangDiem->dotBaoCao->hoiDong->id,
                    'ten_hoi_dong' => $bangDiem->dotBaoCao->hoiDong->ten_hoi_dong
                ] : 'N/A',
                'lich_chams' => $bangDiem->dotBaoCao->lichChams->map(function($lichCham) {
                    return [
                        'id' => $lichCham->id,
                        'hoi_dong' => $lichCham->hoiDong ? [
                            'id' => $lichCham->hoiDong->id,
                            'ten_hoi_dong' => $lichCham->hoiDong->ten_hoi_dong
                        ] : 'N/A'
                    ];
                })
            ]
        ];

        return response()->json($debugInfo);
    }

    /**
     * Debug đơn giản cho đề tài mới
     */
    public function debugSimple()
    {
        $giangVienId = Auth::id();

        // Lấy đề tài mới nhất trong lịch chấm
        $deTaiMoiNhat = LichCham::with(['deTai.nhom.sinhViens', 'dotBaoCao'])
            ->orderBy('created_at', 'desc')
            ->first();

        // Kiểm tra phân công chấm cho đề tài này
        $phanCongCham = null;
        if ($deTaiMoiNhat) {
            $phanCongCham = PhanCongCham::where('de_tai_id', $deTaiMoiNhat->de_tai_id)
                ->where(function($query) use ($giangVienId) {
                    $query->where('giang_vien_phan_bien_id', $giangVienId)
                          ->orWhere('giang_vien_khac_id', $giangVienId);
                })
                ->first();
        }

        $debugData = [
            'giang_vien_id' => $giangVienId,
            'de_tai_moi_nhat' => $deTaiMoiNhat ? [
                'lich_cham_id' => $deTaiMoiNhat->id,
                'de_tai_id' => $deTaiMoiNhat->de_tai_id,
                'ten_de_tai' => $deTaiMoiNhat->deTai ? $deTaiMoiNhat->deTai->ten_de_tai : 'N/A',
                'nhom_id' => $deTaiMoiNhat->deTai && $deTaiMoiNhat->deTai->nhom ? $deTaiMoiNhat->deTai->nhom->id : 'N/A',
                'ten_nhom' => $deTaiMoiNhat->deTai && $deTaiMoiNhat->deTai->nhom ? $deTaiMoiNhat->deTai->nhom->ten : 'N/A',
                'so_sinh_vien' => $deTaiMoiNhat->deTai && $deTaiMoiNhat->deTai->nhom ? $deTaiMoiNhat->deTai->nhom->sinhViens->count() : 0,
                'dot_bao_cao' => $deTaiMoiNhat->dotBaoCao ? $deTaiMoiNhat->dotBaoCao->ten_dot_bao_cao : 'N/A',
                'lich_tao' => $deTaiMoiNhat->lich_tao,
                'created_at' => $deTaiMoiNhat->created_at,
            ] : 'Không có đề tài nào trong lịch chấm',
            'phan_cong_cham' => $phanCongCham ? [
                'id' => $phanCongCham->id,
                'giang_vien_phan_bien_id' => $phanCongCham->giang_vien_phan_bien_id,
                'giang_vien_khac_id' => $phanCongCham->giang_vien_khac_id,
                'co_phan_cong' => $phanCongCham->giang_vien_phan_bien_id == $giangVienId || $phanCongCham->giang_vien_khac_id == $giangVienId,
            ] : 'Không có phân công chấm cho đề tài này',
            'tat_ca_phan_cong_cham' => PhanCongCham::where(function($query) use ($giangVienId) {
                $query->where('giang_vien_phan_bien_id', $giangVienId)
                      ->orWhere('giang_vien_khac_id', $giangVienId);
            })->count(),
        ];

        return response()->json($debugData);
    }
}
