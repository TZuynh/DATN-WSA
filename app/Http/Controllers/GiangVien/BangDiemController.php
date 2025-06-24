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
use Illuminate\Support\Facades\Log;

class BangDiemController extends Controller
{
    /**
     * Hiển thị danh sách sinh viên cần chấm điểm
     */
    public function index()
    {
        $giangVienId = (int)Auth::id();

        // Lấy các phân công chấm có vai trò của giảng viên hiện tại trong hội đồng và có lịch chấm (có đợt báo cáo)
        $phanCongChams = PhanCongCham::with(['deTai.nhom.sinhViens', 'deTai.lichCham', 'hoiDong.phanCongVaiTros'])
            ->whereNotNull('hoi_dong_id')
            ->whereHas('hoiDong.phanCongVaiTros', function ($query) use ($giangVienId) {
                $query->where('tai_khoan_id', $giangVienId);
            })
            ->whereHas('deTai.lichCham') // Chỉ lấy đề tài đã có lịch chấm
            ->get();

        // Lọc phân công hợp lệ
        $phanCongChamsFiltered = $phanCongChams->filter(function ($phanCong) {
            return $phanCong->deTai
                && $phanCong->deTai->nhom
                && $phanCong->deTai->nhom->sinhViens->count() > 0
                && $phanCong->deTai->lichCham
                && $phanCong->deTai->lichCham->dot_bao_cao_id;
        });

        $coDeTaiNhungKhongCoLichCham = $phanCongChams->contains(function ($phanCong) {
            return $phanCong->deTai && !$phanCong->deTai->lichCham;
        });

        $phanCongChamsFiltered = $phanCongChams->map(function($phanCong) use ($giangVienId) {
            $phanCong->vai_tro_cham = 'Không xác định';

            if ($phanCong->hoiDong && $phanCong->hoiDong->phanCongVaiTros) {
                $vaiTro = $phanCong->hoiDong->phanCongVaiTros->firstWhere('tai_khoan_id', $giangVienId);
                if ($vaiTro) {
                    switch ($vaiTro->loai_giang_vien) {
                        case 'Giảng Viên Hướng Dẫn':
                            $phanCong->vai_tro_cham = 'Hướng dẫn';
                            break;
                        case 'Giảng Viên Phản Biện':
                            $phanCong->vai_tro_cham = 'Phản biện';
                            break;
                        case 'Giảng Viên Khác':
                            $phanCong->vai_tro_cham = 'Giảng viên khác';
                            break;
                        default:
                            $phanCong->vai_tro_cham = $vaiTro->loai_giang_vien;
                    }
                }
            }

            return $phanCong;
        });

        $bangDiems = BangDiem::where('giang_vien_id', $giangVienId)
            ->with(['sinhVien', 'dotBaoCao'])
            ->orderBy('created_at', 'desc')
            ->get();

        $bangDiems = $bangDiems->map(function($bangDiem) use ($giangVienId) {
            $bangDiem->vai_tro_cham = 'Không xác định';

            $phanCongCham = PhanCongCham::with('hoiDong.phanCongVaiTros', 'deTai.nhom.sinhViens')
                ->whereHas('deTai.nhom.sinhViens', function($query) use ($bangDiem) {
                    $query->where('sinh_viens.id', $bangDiem->sinh_vien_id);
                })
                ->whereHas('hoiDong.phanCongVaiTros', function($query) use ($giangVienId) {
                    $query->where('tai_khoan_id', $giangVienId);
                })
                ->first();

            if ($phanCongCham && $phanCongCham->hoiDong) {
                $vaiTro = $phanCongCham->hoiDong->phanCongVaiTros->firstWhere('tai_khoan_id', $giangVienId);
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

            return $bangDiem;
        });

        return view('giangvien.bang-diem.index', compact(
            'phanCongChamsFiltered',
            'bangDiems',
            'coDeTaiNhungKhongCoLichCham'
        ));
    }

    /**
     * Hiển thị form chấm điểm
     */
    public function create($sinhVienId, $dotBaoCaoId = null)
    {
        // Kiểm tra sinh viên có thuộc nhóm có đề tài đã có lịch chấm không
        $chiTietNhom = \App\Models\ChiTietNhom::where('sinh_vien_id', $sinhVienId)->first();
        if (!$chiTietNhom || !$chiTietNhom->nhom || !$chiTietNhom->nhom->deTai) {
            return redirect()->route('giangvien.bang-diem.index')->with('error', 'Chỉ có thể chấm điểm khi đề tài đã có lịch chấm.');
        }
        $deTai = $chiTietNhom->nhom->deTai;
        if (!$deTai || !\App\Models\LichCham::where('de_tai_id', $deTai->id)->exists()) {
            return redirect()->route('giangvien.bang-diem.index')->with('error', 'Chỉ có thể chấm điểm khi đề tài đã có lịch chấm.');
        }

        $bangDiem = new BangDiem();
        $bangDiem->sinh_vien_id = $sinhVienId;
        $bangDiem->dot_bao_cao_id = $dotBaoCaoId;

        // Kiểm tra điểm cũ
        $diemCu = null;
        if (request()->has('diemCuId')) {
            $diemCu = BangDiem::findOrFail(request()->diemCuId);
        } else {
            // Tìm điểm cũ không có đợt báo cáo
            $diemCu = BangDiem::where('sinh_vien_id', $sinhVienId)
                ->whereNull('dot_bao_cao_id')
                ->where('giang_vien_id', Auth::id())
                ->first();
        }

        // Nếu có điểm cũ và là điểm của giảng viên hiện tại
        if ($diemCu && $diemCu->giang_vien_id === Auth::id()) {
            $bangDiem->diem_bao_cao = $diemCu->diem_bao_cao;
            $bangDiem->diem_thuyet_trinh = $diemCu->diem_thuyet_trinh;
            $bangDiem->binh_luan = $diemCu->binh_luan;
        }

        $hasDotBaoCao = $dotBaoCaoId !== null;

        // Lấy thông tin sinh viên
        $sinhVien = SinhVien::with('lop')->findOrFail($sinhVienId);

        // Lấy thông tin đợt báo cáo nếu có
        $dotBaoCao = null;
        $tenNhom = 'N/A';
        $tenDeTai = 'N/A';
        if ($dotBaoCaoId) {
            $dotBaoCao = DotBaoCao::with(['lichChams.hoiDong', 'hocKy'])->findOrFail($dotBaoCaoId);
            
            // Lấy tên nhóm và tên đề tài
            $chiTietNhom = \App\Models\ChiTietNhom::where('sinh_vien_id', $sinhVienId)->first();
            if ($chiTietNhom && $chiTietNhom->nhom) {
                $tenNhom = $chiTietNhom->nhom->ten;
                if ($chiTietNhom->nhom->deTai) {
                    $tenDeTai = $chiTietNhom->nhom->deTai->ten_de_tai;
                }
            }
        }

        // Lấy vai trò chấm điểm
        $giangVienId = Auth::id();
        $vaiTroCham = null;
        $chiTietNhom = \App\Models\ChiTietNhom::where('sinh_vien_id', $sinhVienId)->first();
        if ($chiTietNhom && $chiTietNhom->nhom) {
            $nhom = $chiTietNhom->nhom;
            $deTai = $nhom->deTai ? $nhom->deTai : \App\Models\DeTai::where('nhom_id', $nhom->id)->first();
            if ($deTai && $deTai->phanCongCham) {
                $phanCongCham = $deTai->phanCongCham;
                if ($phanCongCham->hoiDong && $phanCongCham->hoiDong->phanCongVaiTros) {
                    $phanCongVaiTro = $phanCongCham->hoiDong->phanCongVaiTros->firstWhere('tai_khoan_id', $giangVienId);
                    $vaiTroCham = $phanCongVaiTro ? $phanCongVaiTro->loai_giang_vien : null;
                }
            }
        }

        return view('giangvien.bang-diem.create', compact(
            'bangDiem',
            'hasDotBaoCao',
            'vaiTroCham',
            'sinhVien',
            'dotBaoCao',
            'tenNhom',
            'tenDeTai'
        ));
    }

    /**
     * Lưu điểm mới
     */
    public function store(Request $request)
    {
        // Kiểm tra sinh viên có thuộc nhóm có đề tài đã có lịch chấm không
        $chiTietNhom = \App\Models\ChiTietNhom::where('sinh_vien_id', $request->sinh_vien_id)->first();
        if (!$chiTietNhom || !$chiTietNhom->nhom || !$chiTietNhom->nhom->deTai) {
            return redirect()->route('giangvien.bang-diem.index')->with('error', 'Chỉ có thể chấm điểm khi đề tài đã có lịch chấm.');
        }
        $deTai = $chiTietNhom->nhom->deTai;
        if (!$deTai || !\App\Models\LichCham::where('de_tai_id', $deTai->id)->exists()) {
            return redirect()->route('giangvien.bang-diem.index')->with('error', 'Chỉ có thể chấm điểm khi đề tài đã có lịch chấm.');
        }

        $rules = [
            'sinh_vien_id' => 'required|exists:sinh_viens,id',
            'binh_luan' => 'nullable|string|max:1000'
        ];

        $hasDotBaoCao = $request->filled('dot_bao_cao_id');

        // Tìm điểm cũ
        $diemCu = BangDiem::where('sinh_vien_id', $request->sinh_vien_id)
            ->whereNull('dot_bao_cao_id')
            ->where('giang_vien_id', Auth::id())
            ->first();

        // Nếu không có đợt báo cáo, yêu cầu nhập điểm báo cáo và thuyết trình
        if (!$hasDotBaoCao) {
            $rules = array_merge($rules, [
                'diem_bao_cao' => 'required|numeric|min:0|max:10',
                'diem_thuyet_trinh' => 'required|numeric|min:0|max:10',
            ]);
        } else {
            // Nếu có đợt báo cáo, điểm báo cáo và thuyết trình có thể null
            $rules = array_merge($rules, [
                'diem_bao_cao' => 'nullable|numeric|min:0|max:10',
                'diem_thuyet_trinh' => 'nullable|numeric|min:0|max:10',
            ]);
        }

        if ($hasDotBaoCao) {
            $rules = array_merge($rules, [
                'diem_bao_cao' => 'nullable|numeric|min:0|max:10',
                'diem_thuyet_trinh' => 'nullable|numeric|min:0|max:10',
                'dot_bao_cao_id' => 'required|exists:dot_bao_caos,id',
                'diem_demo' => 'required|numeric|min:0|max:10',
                'diem_cau_hoi' => 'required|numeric|min:0|max:10',
                'diem_cong' => 'nullable|numeric|min:0|max:2',
            ]);
        }

        $validated = $request->validate($rules);

        try {
            $data = [
                'sinh_vien_id' => $request->sinh_vien_id,
                'dot_bao_cao_id' => $request->dot_bao_cao_id,
                'giang_vien_id' => Auth::id(),
                'binh_luan' => $request->binh_luan
            ];

            // Nếu có đợt báo cáo, lấy điểm báo cáo và thuyết trình từ điểm cũ nếu có
            if ($hasDotBaoCao) {
                if ($diemCu) {
                    $data['diem_bao_cao'] = $diemCu->diem_bao_cao;
                    $data['diem_thuyet_trinh'] = $diemCu->diem_thuyet_trinh;
                } else {
                    $data['diem_bao_cao'] = $request->diem_bao_cao;
                    $data['diem_thuyet_trinh'] = $request->diem_thuyet_trinh;
                }
                $data['diem_demo'] = $request->diem_demo;
                $data['diem_cau_hoi'] = $request->diem_cau_hoi;
                $data['diem_cong'] = $request->diem_cong ?? 0;
            } else {
                $data['diem_bao_cao'] = $request->diem_bao_cao;
                $data['diem_thuyet_trinh'] = $request->diem_thuyet_trinh;
            }

            BangDiem::create($data);

            return redirect()->route('giangvien.bang-diem.index')
                ->with('success', 'Chấm điểm thành công.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi lưu điểm: ' . $e->getMessage());
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
        // Kiểm tra đề tài có lịch chấm không
        $chiTietNhom = \App\Models\ChiTietNhom::where('sinh_vien_id', $bangDiem->sinh_vien_id)->first();
        if (!$chiTietNhom || !$chiTietNhom->nhom || !$chiTietNhom->nhom->deTai) {
            return redirect()->route('giangvien.bang-diem.index')->with('error', 'Chỉ có thể sửa điểm khi đề tài đã có lịch chấm.');
        }
        $deTai = $chiTietNhom->nhom->deTai;
        if (!$deTai || !\App\Models\LichCham::where('de_tai_id', $deTai->id)->exists()) {
            return redirect()->route('giangvien.bang-diem.index')->with('error', 'Chỉ có thể sửa điểm khi đề tài đã có lịch chấm.');
        }

        // Xác định có đợt báo cáo hay không
        $hasDotBaoCao = $bangDiem->dot_bao_cao_id !== null;
        $vaiTroCham = null;
        $canEditBaoCaoAndThuyetTrinh = true;

        // Lấy vai trò chấm điểm
        $giangVienId = Auth::id();
        $chiTietNhom = \App\Models\ChiTietNhom::where('sinh_vien_id', $bangDiem->sinh_vien_id)->first();
        if ($chiTietNhom && $chiTietNhom->nhom) {
            $nhom = $chiTietNhom->nhom;
            $deTai = $nhom->deTai ? $nhom->deTai : \App\Models\DeTai::where('nhom_id', $nhom->id)->first();
            if ($deTai && $deTai->phanCongCham) {
                $phanCongCham = $deTai->phanCongCham;
                if ($phanCongCham->hoiDong && $phanCongCham->hoiDong->phanCongVaiTros) {
                    $phanCongVaiTro = $phanCongCham->hoiDong->phanCongVaiTros->firstWhere('tai_khoan_id', $giangVienId);
                    $vaiTroCham = $phanCongVaiTro ? $phanCongVaiTro->loai_giang_vien : null;
                }
            }
        }

        // Kiểm tra xem có được phép sửa điểm báo cáo và thuyết trình không
        $canEditBaoCaoAndThuyetTrinh = !$hasDotBaoCao || 
            (in_array($vaiTroCham, ['Giảng Viên Hướng Dẫn', 'Giảng Viên Phản Biện']) && 
             (!$bangDiem->diem_bao_cao && !$bangDiem->diem_thuyet_trinh));

        Log::info('Edit BangDiem Debug', [
            'bangDiem_id' => $bangDiem->id,
            'hasDotBaoCao' => $hasDotBaoCao,
            'dot_bao_cao_id' => $bangDiem->dot_bao_cao_id,
            'vaiTroCham' => $vaiTroCham,
            'canEditBaoCaoAndThuyetTrinh' => $canEditBaoCaoAndThuyetTrinh
        ]);

        return view('giangvien.bang-diem.edit', compact(
            'bangDiem', 
            'hasDotBaoCao', 
            'vaiTroCham',
            'canEditBaoCaoAndThuyetTrinh'
        ));
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
        // Kiểm tra đề tài có lịch chấm không
        $chiTietNhom = \App\Models\ChiTietNhom::where('sinh_vien_id', $bangDiem->sinh_vien_id)->first();
        if (!$chiTietNhom || !$chiTietNhom->nhom || !$chiTietNhom->nhom->deTai) {
            return redirect()->route('giangvien.bang-diem.index')->with('error', 'Chỉ có thể cập nhật điểm khi đề tài đã có lịch chấm.');
        }
        $deTai = $chiTietNhom->nhom->deTai;
        if (!$deTai || !\App\Models\LichCham::where('de_tai_id', $deTai->id)->exists()) {
            return redirect()->route('giangvien.bang-diem.index')->with('error', 'Chỉ có thể cập nhật điểm khi đề tài đã có lịch chấm.');
        }

        $rules = [];
        $data = [];

        // Kiểm tra xem có được phép sửa điểm báo cáo và thuyết trình không
        $hasDotBaoCao = $bangDiem->dot_bao_cao_id !== null;

        // Nếu không có đợt báo cáo thì mới bắt buộc điểm báo cáo và thuyết trình
        if (!$hasDotBaoCao) {
            $rules = array_merge($rules, [
                'diem_bao_cao' => 'required|numeric|min:0|max:10',
                'diem_thuyet_trinh' => 'required|numeric|min:0|max:10',
            ]);
        } else {
            // Nếu có đợt báo cáo, điểm báo cáo và thuyết trình có thể null
            $rules = array_merge($rules, [
                'diem_bao_cao' => 'nullable|numeric|min:0|max:10',
                'diem_thuyet_trinh' => 'nullable|numeric|min:0|max:10',
            ]);
        }

        // Nếu có đợt báo cáo thì yêu cầu nhập đủ các điểm còn lại
        if ($hasDotBaoCao) {
            $rules = array_merge($rules, [
                'diem_demo' => 'required|numeric|min:0|max:10',
                'diem_cau_hoi' => 'required|numeric|min:0|max:10',
                'diem_cong' => 'nullable|numeric|min:0|max:2',
            ]);
            $data = array_merge($data, [
                'diem_demo' => $request->diem_demo,
                'diem_cau_hoi' => $request->diem_cau_hoi,
                'diem_cong' => $request->diem_cong ?? 0,
            ]);
        }

        $rules['binh_luan'] = 'nullable|string|max:1000';
        $data['binh_luan'] = $request->binh_luan;

        $validated = $request->validate($rules);

        try {
            // Nếu có đợt báo cáo, giữ nguyên điểm báo cáo và thuyết trình cũ
            if ($hasDotBaoCao) {
                $data['diem_bao_cao'] = $bangDiem->diem_bao_cao;
                $data['diem_thuyet_trinh'] = $bangDiem->diem_thuyet_trinh;
            } else {
                $data['diem_bao_cao'] = $request->diem_bao_cao;
                $data['diem_thuyet_trinh'] = $request->diem_thuyet_trinh;
            }

            $bangDiem->update($data);

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
