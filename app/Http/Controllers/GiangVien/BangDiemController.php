<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use App\Models\BangDiem;
use App\Models\PhanCongVaiTro;
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
        $giangVienId = Auth::id();

        // 1. Lấy tất cả phân công (hội đồng + riêng đề tài)
        $phanCongVaiTros = PhanCongVaiTro::with([
            // đề tài của hội đồng
            'hoiDong.chiTietBaoCaos.deTai.nhom.sinhViens',
            'hoiDong.chiTietBaoCaos.deTai.lichCham',
            // đề tài phân công riêng
            'deTai.nhom.sinhViens',
            'deTai.lichCham',
            'vaiTro',
            'taiKhoan',
        ])
        ->where('tai_khoan_id', Auth::id())
        ->get();


        // 2. Lọc phân công riêng theo đề tài (de_tai_id != null)
        $phanCongTheoDeTai = $phanCongVaiTros->filter(fn($pc) => ! is_null($pc->de_tai_id));

        // 3. Gom đề tài được chấm
        $deTaisDuocCham = collect();
        foreach ($phanCongVaiTros as $phanCong) {
            if ($phanCong->de_tai_id) {
                // riêng 1 đề tài
                $deTai = $phanCong->deTai;
                if ($deTai && $deTai->lichCham) {
                    $deTaisDuocCham->push(compact('phanCong', 'deTai'));
                }
            } else {
                // toàn bộ đề tài trong hội đồng
                foreach ($phanCong->hoiDong->chiTietBaoCaos as $chiTiet) {
                    $deTai = $chiTiet->deTai;
                    if ($deTai && $deTai->lichCham) {
                        $deTaisDuocCham->push(compact('phanCong', 'deTai'));
                    }
                }
            }
        }

        // 4. Tách xuống danh sách sinh viên
        $dsSinhVien = collect();
        foreach ($deTaisDuocCham as $item) {
            $phanCong = $item['phanCong'];
            $deTai    = $item['deTai'];
            foreach ($deTai->nhom->sinhViens as $sinhVien) {
                $dsSinhVien->push([
                    'phan_cong_vai_tro' => $phanCong,
                    'sinh_vien'         => $sinhVien,
                    'nhom'              => $deTai->nhom,
                    'de_tai'            => $deTai,
                    'vai_tro_cham'      => $phanCong->vaiTro->ten ?? $phanCong->loai_giang_vien,
                ]);
            }
        }

        // 5. Sắp xếp
        $dsSinhVien = $dsSinhVien
            ->sortBy([['nhom.ten','asc'], ['sinh_vien.ten','asc']])
            ->values();

        // 6. Lấy bảng điểm hiện có
        $bangDiems = BangDiem::where('giang_vien_id', $giangVienId)
            ->with(['sinhVien', 'dotBaoCao'])
            ->orderBy('created_at','desc')
            ->get();

        // 7. Tính xem có đề tài nhưng chưa có lịch chấm
        $coDeTaiNhungKhongCoLichCham = $phanCongTheoDeTai->contains(fn($pc) => ! $pc->deTai->lichCham);

        // 8. (Tuỳ bạn) Tính các điểm trung bình chung
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
                    // Ưu tiên loai_giang_vien nếu có, nếu không thì lấy vai_tro
                    if ($vaiTro->loai_giang_vien) {
                        $bangDiem->vai_tro_cham = $vaiTro->loai_giang_vien;
                    } else {
                        $bangDiem->vai_tro_cham = $vaiTro->vaiTro->ten ?? 'Không xác định';
                    }
                }
            }

            return $bangDiem;
        });

        // Gom nhóm bảng điểm theo giảng viên để tính trung bình báo cáo, tổng điểm trung bình, điểm tổng kết
        $allGiangVienIds = $bangDiems->pluck('giang_vien_id')->unique();
        $dsDiemBaoCaoTB = collect();
        $dsTongDiemTB = collect();
        $dsDiemTongKet = collect();
        foreach ($allGiangVienIds as $giangVienId) {
            $dsBangDiemGV = $bangDiems->where('giang_vien_id', $giangVienId);
            // Tính trung bình điểm báo cáo
            $diemBaoCaoArr = $dsBangDiemGV->pluck('diem_bao_cao')->filter(function($v){ return $v !== null; });
            $diemBaoCaoTB = $diemBaoCaoArr->count() > 0 ? $diemBaoCaoArr->avg() : 0;
            // Tính tổng điểm trung bình
            $tongDiemArr = $dsBangDiemGV->map(function($bd) {
                return ($bd->diem_thuyet_trinh ?? 0) + ($bd->diem_demo ?? 0) + ($bd->diem_cau_hoi ?? 0) + ($bd->diem_cong ?? 0);
            });
            $tongDiemTB = $tongDiemArr->count() > 0 ? $tongDiemArr->avg() : 0;
            // Điểm tổng kết
            $diemTongKet = ($diemBaoCaoTB !== 0 && $tongDiemTB !== 0) ? ($diemBaoCaoTB * 0.2 + $tongDiemTB * 0.8) : 0;
            // Nếu cả điểm báo cáo TB và tổng điểm TB đều bằng 0 thì bỏ qua giảng viên này
            if ($diemBaoCaoTB > 0 || $tongDiemTB > 0) {
                $dsDiemBaoCaoTB->push($diemBaoCaoTB);
                $dsTongDiemTB->push($tongDiemTB);
                $dsDiemTongKet->push($diemTongKet);
            }
        }
        // Tính trung bình chung các giá trị trên
        $diemTrungBinhBaoCaoChung = $dsDiemBaoCaoTB->count() > 0 ? round($dsDiemBaoCaoTB->avg(), 2) : null;
        $tongDiemTrungBinhChung = $dsTongDiemTB->count() > 0 ? round($dsTongDiemTB->avg(), 2) : null;
        $diemTongKetChung = $dsDiemTongKet->count() > 0 ? round($dsDiemTongKet->avg(), 2) : null;

        // 9. Trả về view, chắc chắn include đủ biến
        return view('giangvien.bang-diem.index', [
            'dsSinhVien'                 => $dsSinhVien,
            'bangDiems'                  => $bangDiems,
            'phanCongTheoDeTai'          => $phanCongTheoDeTai,
            'coDeTaiNhungKhongCoLichCham'=> $coDeTaiNhungKhongCoLichCham,
            'diemTrungBinhBaoCaoChung' => $diemTrungBinhBaoCaoChung,
            'tongDiemTrungBinhChung'   => $tongDiemTrungBinhChung,
            'diemTongKetChung'         => $diemTongKetChung,
        ]);
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

        // Kiểm tra giảng viên có được phân công vào hội đồng chứa đề tài này không
        $giangVienId = Auth::id();
        $phanCongVaiTro = \App\Models\PhanCongVaiTro::with(['hoiDong.chiTietBaoCaos.deTai'])
            ->where('tai_khoan_id', $giangVienId)
            ->whereNull('de_tai_id')
            ->whereHas('hoiDong.chiTietBaoCaos.deTai', function($query) use ($deTai) {
                $query->where('de_tais.id', $deTai->id);
            })
            ->first();

        if (!$phanCongVaiTro) {
            return redirect()->route('giangvien.bang-diem.index')->with('error', 'Bạn chưa được phân công vào hội đồng chấm đề tài này.');
        }

        // Kiểm tra GVHD và GVPB đã đồng ý chưa
        $hoiDong = $phanCongVaiTro->hoiDong;
        $phanCongVaiTros = $hoiDong->phanCongVaiTros ?? collect();

        $gvhdDongY = $phanCongVaiTros->where('loai_giang_vien', 'Giảng Viên Hướng Dẫn')->where('trang_thai', 'đồng ý')->count() > 0;
        $gvpbDongY = $phanCongVaiTros->where('loai_giang_vien', 'Giảng Viên Phản Biện')->where('trang_thai', 'đồng ý')->count() > 0;

        if (!($gvhdDongY && $gvpbDongY)) {
            return redirect()->route('giangvien.bang-diem.index')->with('error', 'Chỉ được chấm khi GVHD và GVPB đã đồng ý.');
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
        $vaiTroCham = $phanCongVaiTro->vaiTro->ten ?? $phanCongVaiTro->loai_giang_vien;

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
        $nhom = $chiTietNhom ? $chiTietNhom->nhom : null;
        $deTai = $nhom ? $nhom->deTai : null;
        if (!$deTai && $nhom) {
            $deTai = \App\Models\DeTai::where('nhom_id', $nhom->id)->first();
        }

        // Kiểm tra giảng viên có được phân công vào hội đồng chứa đề tài này không
        $giangVienId = Auth::id();
        $phanCongVaiTro = \App\Models\PhanCongVaiTro::with(['hoiDong.chiTietBaoCaos.deTai'])
            ->where('tai_khoan_id', $giangVienId)
            ->whereNull('de_tai_id')
            ->whereHas('hoiDong.chiTietBaoCaos.deTai', function($query) use ($deTai) {
                $query->where('de_tais.id', $deTai->id);
            })
            ->first();

        if (!$phanCongVaiTro) {
            return redirect()->route('giangvien.bang-diem.index')->with('error', 'Bạn chưa được phân công vào hội đồng chấm đề tài này.');
        }

        // Kiểm tra GVHD và GVPB đã đồng ý chưa
        $hoiDong = $phanCongVaiTro->hoiDong;
        $phanCongVaiTros = $hoiDong->phanCongVaiTros ?? collect();

        $gvhdDongY = $phanCongVaiTros->where('loai_giang_vien', 'Giảng Viên Hướng Dẫn')->where('trang_thai', 'đồng ý')->count() > 0;
        $gvpbDongY = $phanCongVaiTros->where('loai_giang_vien', 'Giảng Viên Phản Biện')->where('trang_thai', 'đồng ý')->count() > 0;

        if (!($gvhdDongY && $gvpbDongY)) {
            return redirect()->route('giangvien.bang-diem.index')->with('error', 'Chỉ được chấm khi GVHD và GVPB đã đồng ý.');
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
                'diem_thuyet_trinh' => 'nullable|numeric|min:0|max:3',
                'dot_bao_cao_id' => 'required|exists:dot_bao_caos,id',
                'diem_demo' => 'required|numeric|min:0|max:4',
                'diem_cau_hoi' => 'required|numeric|min:0|max:1',
                'diem_cong' => 'nullable|numeric|min:0|max:1',
            ]);
        }

        $messages = [
            'diem_thuyet_trinh.max' => 'Điểm thuyết trình tối đa là 3.',
            'diem_demo.max' => 'Điểm demo tối đa là 4.',
            'diem_cau_hoi.max' => 'Điểm câu hỏi tối đa là 1.',
            'diem_cong.max' => 'Điểm cộng tối đa là 1.',
        ];
        $validated = $request->validate($rules, $messages);

        try {
            $data = [
                'sinh_vien_id' => $request->sinh_vien_id,
                'dot_bao_cao_id' => $request->dot_bao_cao_id,
                'giang_vien_id' => Auth::id(),
                'binh_luan' => $request->binh_luan,
                'de_tai_id' => $deTai ? $deTai->id : null
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

                // Xác định vai trò chấm điểm
                $vaiTroCham = $phanCongVaiTro->vaiTro->ten ?? $phanCongVaiTro->loai_giang_vien;

                // Lưu điểm báo cáo vào đúng cột
                if (in_array($vaiTroCham, ['Giảng Viên Hướng Dẫn', 'Giảng Viên Phản Biện'])) {
                    $data['diem_bao_cao'] = $request->diem_bao_cao;
                }
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

        // Kiểm tra giảng viên có được phân công vào hội đồng chứa đề tài này không
        $giangVienId = Auth::id();
        $phanCongVaiTro = \App\Models\PhanCongVaiTro::with(['hoiDong.chiTietBaoCaos.deTai'])
            ->where('tai_khoan_id', $giangVienId)
            ->whereNull('de_tai_id')
            ->whereHas('hoiDong.chiTietBaoCaos.deTai', function($query) use ($deTai) {
                $query->where('de_tais.id', $deTai->id);
            })
            ->first();

        if (!$phanCongVaiTro) {
            return redirect()->route('giangvien.bang-diem.index')->with('error', 'Bạn chưa được phân công vào hội đồng chấm đề tài này.');
        }

        // Kiểm tra GVHD và GVPB đã đồng ý chưa
        $hoiDong = $phanCongVaiTro->hoiDong;
        $phanCongVaiTros = $hoiDong->phanCongVaiTros ?? collect();

        $gvhdDongY = $phanCongVaiTros->where('loai_giang_vien', 'Giảng Viên Hướng Dẫn')->where('trang_thai', 'đồng ý')->count() > 0;
        $gvpbDongY = $phanCongVaiTros->where('loai_giang_vien', 'Giảng Viên Phản Biện')->where('trang_thai', 'đồng ý')->count() > 0;

        if (!($gvhdDongY && $gvpbDongY)) {
            return redirect()->route('giangvien.bang-diem.index')->with('error', 'Chỉ được chấm khi GVHD và GVPB đã đồng ý.');
        }

        // Xác định có đợt báo cáo hay không
        $hasDotBaoCao = $bangDiem->dot_bao_cao_id !== null;
        $vaiTroCham = $phanCongVaiTro->vaiTro->ten ?? $phanCongVaiTro->loai_giang_vien;
        $canEditBaoCaoAndThuyetTrinh = in_array($vaiTroCham, ['Giảng Viên Hướng Dẫn', 'Giảng Viên Phản Biện']);

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

        // Kiểm tra giảng viên có được phân công vào hội đồng chứa đề tài này không
        $giangVienId = Auth::id();
        $phanCongVaiTro = \App\Models\PhanCongVaiTro::with(['hoiDong.chiTietBaoCaos.deTai'])
            ->where('tai_khoan_id', $giangVienId)
            ->whereNull('de_tai_id')
            ->whereHas('hoiDong.chiTietBaoCaos.deTai', function($query) use ($deTai) {
                $query->where('de_tais.id', $deTai->id);
            })
            ->first();

        if (!$phanCongVaiTro) {
            return redirect()->route('giangvien.bang-diem.index')->with('error', 'Bạn chưa được phân công vào hội đồng chấm đề tài này.');
        }

        // Kiểm tra GVHD và GVPB đã đồng ý chưa
        $hoiDong = $phanCongVaiTro->hoiDong;
        $phanCongVaiTros = $hoiDong->phanCongVaiTros ?? collect();

        $gvhdDongY = $phanCongVaiTros->where('loai_giang_vien', 'Giảng Viên Hướng Dẫn')->where('trang_thai', 'đồng ý')->count() > 0;
        $gvpbDongY = $phanCongVaiTros->where('loai_giang_vien', 'Giảng Viên Phản Biện')->where('trang_thai', 'đồng ý')->count() > 0;

        if (!($gvhdDongY && $gvpbDongY)) {
            return redirect()->route('giangvien.bang-diem.index')->with('error', 'Chỉ được chấm khi GVHD và GVPB đã đồng ý.');
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
            // Nếu có đợt báo cáo, chỉ cho phép GVHD và GV Phản Biện sửa điểm báo cáo/thuyết trình
            if ($hasDotBaoCao) {
                // Lấy vai trò chấm điểm
                $vaiTroCham = $phanCongVaiTro->vaiTro->ten ?? $phanCongVaiTro->loai_giang_vien;
                if (in_array($vaiTroCham, ['Giảng Viên Hướng Dẫn', 'Giảng Viên Phản Biện'])) {
                    $data['diem_bao_cao'] = $request->diem_bao_cao;
                } else {
                    $data['diem_bao_cao'] = $bangDiem->diem_bao_cao;
                }
                $data['diem_thuyet_trinh'] = $request->diem_thuyet_trinh;
            } else {
                $data['diem_bao_cao'] = $request->diem_bao_cao;
                $data['diem_thuyet_trinh'] = $request->diem_thuyet_trinh;
            }

            $bangDiem->update(array_merge($data, [
                'de_tai_id' => $deTai ? $deTai->id : null
            ]));

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
            'phan_cong_chams_for_giang_vien' => PhanCongCham::whereHas('hoiDong.phanCongVaiTros', function($query) use ($giangVienId) {
                $query->where('tai_khoan_id', $giangVienId);
            })->count(),
            'lich_chams_total' => LichCham::count(),
            'de_tais_total' => DeTai::count(),
            'nhoms_total' => Nhom::count(),
            'sinh_viens_total' => SinhVien::count(),
            'dot_bao_caos_total' => DotBaoCao::count(),
            'bang_diems_total' => BangDiem::count(),
        ];

        // Kiểm tra chi tiết phân công chấm
        $phanCongChamQuery = PhanCongCham::whereHas('hoiDong.phanCongVaiTros', function($query) use ($giangVienId) {
            $query->where('tai_khoan_id', $giangVienId);
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
                ->whereHas('hoiDong.phanCongVaiTros', function($query) use ($giangVienId) {
                    $query->where('tai_khoan_id', $giangVienId);
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
                'co_phan_cong' => true,
            ] : 'Không có phân công chấm cho đề tài này',
            'tat_ca_phan_cong_cham' => PhanCongCham::whereHas('hoiDong.phanCongVaiTros', function($query) use ($giangVienId) {
                $query->where('tai_khoan_id', $giangVienId);
            })->count(),
        ];

        return response()->json($debugData);
    }
}
