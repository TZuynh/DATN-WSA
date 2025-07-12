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
        ->where('tai_khoan_id', $giangVienId)
        ->get();
    
        // 2. Lọc phân công riêng theo đề tài (de_tai_id != null)
        $phanCongTheoDeTai = $phanCongVaiTros
        ->filter(fn($pc) => !is_null($pc->de_tai_id))
        ->map(function($pc) {
            return [
                'phan_cong'    => $pc,
                'de_tai'       => $pc->deTai,
                'nhom'         => $pc->deTai->nhom ?? null,
                'lich'         => $pc->deTai->lichCham ?? null,
                'vai_tro_cham' => $this->getVaiTroCham($pc),
            ];
        })
        ->values();    
    
        // 3. Gom đề tài được chấm, loại trùng (chỉ lấy mỗi đề tài 1 lần)
        $deTaiIdsAdded = [];
        $deTaisDuocCham = collect();
    
        foreach ($phanCongVaiTros as $phanCong) {
            if ($phanCong->de_tai_id) {
                // Phân công riêng 1 đề tài
                $deTai = $phanCong->deTai;
                if ($deTai && !in_array($deTai->id, $deTaiIdsAdded)) {
                    $deTaisDuocCham->push(compact('phanCong', 'deTai'));
                    $deTaiIdsAdded[] = $deTai->id;
                }
            } else {
                // Toàn bộ đề tài trong hội đồng
                if ($phanCong->hoiDong && $phanCong->hoiDong->chiTietBaoCaos) {
                    foreach ($phanCong->hoiDong->chiTietBaoCaos as $chiTiet) {
                        $deTai = $chiTiet->deTai;
                        if ($deTai && !in_array($deTai->id, $deTaiIdsAdded)) {
                            $deTaisDuocCham->push(compact('phanCong', 'deTai'));
                            $deTaiIdsAdded[] = $deTai->id;
                        }
                    }
                }
            }
        }        
    
        // 4. Tách xuống danh sách sinh viên
        $dsSinhVien = collect();
        foreach ($deTaisDuocCham as $item) {
            $phanCong = $item['phanCong'];
            $deTai    = $item['deTai'];
            $dot_bao_cao_id = $deTai->lichCham && $deTai->lichCham->dot_bao_cao_id ? $deTai->lichCham->dot_bao_cao_id : null;
        
            if ($deTai->nhom && $deTai->nhom->sinhViens) {
                foreach ($deTai->nhom->sinhViens as $sinhVien) {
                    $dsSinhVien->push([
                        'phan_cong_vai_tro' => $phanCong,
                        'sinh_vien'         => $sinhVien,
                        'nhom'              => $deTai->nhom,
                        'de_tai'            => $deTai,
                        'vai_tro_cham'      => $this->getVaiTroCham($phanCong),
                        'dot_bao_cao_id'    => $dot_bao_cao_id, // luôn có field này, có thể null
                    ]);
                }
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
        $coDeTaiNhungKhongCoLichCham = $phanCongTheoDeTai->contains(fn($pc) => !optional($pc['de_tai'])->lichCham);
    
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
            'diemTrungBinhBaoCaoChung'   => $diemTrungBinhBaoCaoChung,
            'tongDiemTrungBinhChung'     => $tongDiemTrungBinhChung,
            'diemTongKetChung'           => $diemTongKetChung,
        ]);
    }

    /**
     * Hiển thị form chấm điểm
     */
    public function create($sinhVienId, $dotBaoCaoId = null)
    {
        // Lấy chi tiết nhóm, đề tài
        $chiTietNhom = \App\Models\ChiTietNhom::where('sinh_vien_id', $sinhVienId)->first();
        if (!$chiTietNhom || !$chiTietNhom->nhom || !$chiTietNhom->nhom->deTai) {
            return redirect()->route('giangvien.bang-diem.index')->with('error', 'Chỉ có thể chấm điểm khi đề tài đã có lịch chấm.');
        }
        $deTai = $chiTietNhom->nhom->deTai;
        
        // Lấy phân công vai trò của giảng viên
        $giangVienId = Auth::id();
        
        // Kiểm tra phân công riêng trước
        $phanCongVaiTro = PhanCongVaiTro::with(['deTai.nhom.sinhViens'])
            ->where('tai_khoan_id', $giangVienId)
            ->where('de_tai_id', $deTai->id)
            ->first();
            
        // Nếu không có phân công riêng, kiểm tra phân công hội đồng
        if (!$phanCongVaiTro) {
            $phanCongVaiTro = PhanCongVaiTro::with(['hoiDong.chiTietBaoCaos.deTai'])
                ->where('tai_khoan_id', $giangVienId)
                ->whereNull('de_tai_id')
                ->whereHas('hoiDong.chiTietBaoCaos.deTai', function($query) use ($deTai) {
                    $query->where('de_tais.id', $deTai->id);
                })
                ->first();
        }

        if (!$phanCongVaiTro) {
            return redirect()->route('giangvien.bang-diem.index')->with('error', 'Bạn chưa được phân công chấm đề tài này.');
        }
        
        // Kiểm tra quyền chấm điểm báo cáo và thuyết trình
        $loaiGiangVien = $phanCongVaiTro->loai_giang_vien;
        $vaiTro = $phanCongVaiTro->vaiTro->ten ?? null;
        
        // Chỉ cho phép chấm điểm báo cáo nếu có loại giảng viên là hướng dẫn hoặc phản biện
        $canGradeBaoCao = in_array($loaiGiangVien, ['Giảng Viên Hướng Dẫn', 'Giảng Viên Phản Biện']);
        
        // Cho phép chấm điểm thuyết trình, demo, câu hỏi, cộng nếu có loại giảng viên hoặc là vai trò khác
        $canGradeOtherScores = !is_null($loaiGiangVien) || in_array($vaiTro, ['Trưởng tiểu ban', 'Thư ký', 'Thành viên']);
        
        // Không disable bất kỳ trường nào, chỉ kiểm tra quyền khi hiển thị
        $shouldDisableBasicScores = false;
        
        // Logic cũ để tương thích với view hiện tại
        $canGradeBaoCaoAndThuyetTrinh = $canGradeOtherScores;

        // Bỏ điều kiện kiểm tra GVHD và GVPB phải đồng ý để cho phép chấm điểm
        // $hoiDong = $phanCongVaiTro->hoiDong;
        // $phanCongVaiTros = $hoiDong->phanCongVaiTros ?? collect();
        // $gvhdDongY = $phanCongVaiTros->where('loai_giang_vien', 'Giảng Viên Hướng Dẫn')->where('trang_thai', 'đồng ý')->count() > 0;
        // $gvpbDongY = $phanCongVaiTros->where('loai_giang_vien', 'Giảng Viên Phản Biện')->where('trang_thai', 'đồng ý')->count() > 0;
        // if (!($gvhdDongY && $gvpbDongY)) {
        //     $gvhdStatus = $phanCongVaiTros->where('loai_giang_vien', 'Giảng Viên Hướng Dẫn')->pluck('trang_thai')->toArray();
        //     $gvpbStatus = $phanCongVaiTros->where('loai_giang_vien', 'Giảng Viên Phản Biện')->pluck('trang_thai')->toArray();
        //     return redirect()->route('giangvien.bang-diem.index')
        //         ->with('error', 'Chỉ được chấm khi GVHD và GVPB đã đồng ý. 
        //         GVHD trạng thái: '. implode(', ', $gvhdStatus) .'
        //         GVPB trạng thái: '. implode(', ', $gvpbStatus));
        // }

        // Kiểm tra quyền chỉnh sửa điểm
        // $shouldDisableBasicScores = $canGradeBaoCaoAndThuyetTrinh === false; // This line is no longer needed

        // Còn lại các dữ liệu cần thiết
        $bangDiem = new BangDiem();
        $bangDiem->sinh_vien_id = $sinhVienId;
        $bangDiem->dot_bao_cao_id = $dotBaoCaoId;

        // Kiểm tra điểm cũ
        $diemCu = null;
        if (request()->has('diemCuId')) {
            $diemCu = BangDiem::findOrFail(request()->diemCuId);
        } else {
            $diemCu = BangDiem::where('sinh_vien_id', $sinhVienId)
                ->whereNull('dot_bao_cao_id')
                ->where('giang_vien_id', Auth::id())
                ->first();
        }
        if ($diemCu && $diemCu->giang_vien_id === Auth::id()) {
            $bangDiem->diem_bao_cao = $diemCu->diem_bao_cao;
            $bangDiem->diem_thuyet_trinh = $diemCu->diem_thuyet_trinh;
            $bangDiem->binh_luan = $diemCu->binh_luan;
        }

        $hasDotBaoCao = $dotBaoCaoId !== null;
        $sinhVien = SinhVien::with('lop')->findOrFail($sinhVienId);
        $dotBaoCao = $dotBaoCaoId ? DotBaoCao::with(['lichChams.hoiDong', 'hocKy'])->findOrFail($dotBaoCaoId) : null;

        // Lấy tên nhóm, đề tài
        $tenNhom = 'N/A';
        $tenDeTai = 'N/A';
        if ($chiTietNhom && $chiTietNhom->nhom) {
            $tenNhom = $chiTietNhom->nhom->ten;
            if ($chiTietNhom->nhom->deTai) {
                $tenDeTai = $chiTietNhom->nhom->deTai->ten_de_tai;
            }
        }

        $vaiTroCham = $this->getVaiTroCham($phanCongVaiTro);
        
        return view('giangvien.bang-diem.create', compact(
            'bangDiem',
            'hasDotBaoCao',
            'vaiTroCham',
            'loaiGiangVien',
            'canGradeBaoCao',
            'canGradeOtherScores',
            'canGradeBaoCaoAndThuyetTrinh',
            'shouldDisableBasicScores',
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
        
        // Kiểm tra phân công riêng trước
        $phanCongVaiTro = \App\Models\PhanCongVaiTro::with(['deTai.nhom.sinhViens'])
            ->where('tai_khoan_id', $giangVienId)
            ->where('de_tai_id', $deTai->id)
            ->first();
            
        // Nếu không có phân công riêng, kiểm tra phân công hội đồng
        if (!$phanCongVaiTro) {
            $phanCongVaiTro = \App\Models\PhanCongVaiTro::with(['hoiDong.chiTietBaoCaos.deTai'])
                ->where('tai_khoan_id', $giangVienId)
                ->whereNull('de_tai_id')
                ->whereHas('hoiDong.chiTietBaoCaos.deTai', function($query) use ($deTai) {
                    $query->where('de_tais.id', $deTai->id);
                })
                ->first();
        }

        if (!$phanCongVaiTro) {
            return redirect()->route('giangvien.bang-diem.index')->with('error', 'Bạn chưa được phân công chấm đề tài này.');
        }

        // Kiểm tra quyền chấm điểm báo cáo
        $loaiGiangVien = $phanCongVaiTro->loai_giang_vien;
        $vaiTro = $phanCongVaiTro->vaiTro->ten ?? null;
        $canGradeBaoCao = in_array($loaiGiangVien, ['Giảng Viên Hướng Dẫn', 'Giảng Viên Phản Biện']);
        $canGradeOtherScores = !is_null($loaiGiangVien) || in_array($vaiTro, ['Trưởng tiểu ban', 'Thư ký', 'Thành viên']);

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

        // Điểm báo cáo
        if ($canGradeBaoCao) {
            $rules = array_merge($rules, [
                'diem_bao_cao' => 'nullable|numeric|min:0|max:10',
            ]);
        }
        
        // Điểm thuyết trình và các điểm khác
        if ($canGradeOtherScores) {
            $rules = array_merge($rules, [
                'diem_thuyet_trinh' => 'nullable|numeric|min:0|max:3',
                'diem_demo' => 'nullable|numeric|min:0|max:4',
                'diem_cau_hoi' => 'nullable|numeric|min:0|max:1',
                'diem_cong' => 'nullable|numeric|min:0|max:1',
            ]);
        } else {
            return redirect()->route('giangvien.bang-diem.index')->with('error', 'Bạn không có quyền chấm điểm.');
        }

        // Nếu có dot_bao_cao_id thì validate
        if ($request->filled('dot_bao_cao_id')) {
            $rules = array_merge($rules, [
                'dot_bao_cao_id' => 'required|exists:dot_bao_caos,id',
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

            // Lưu điểm theo quyền chấm
            if ($canGradeBaoCao) {
                $data['diem_bao_cao'] = $request->diem_bao_cao;
            }
            
            if ($canGradeOtherScores) {
                $data['diem_thuyet_trinh'] = $request->diem_thuyet_trinh;
                $data['diem_demo'] = $request->diem_demo;
                $data['diem_cau_hoi'] = $request->diem_cau_hoi;
                $data['diem_cong'] = $request->diem_cong ?? 0;
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
        
        // Kiểm tra phân công riêng trước
        $phanCongVaiTro = \App\Models\PhanCongVaiTro::with(['deTai.nhom.sinhViens'])
            ->where('tai_khoan_id', $giangVienId)
            ->where('de_tai_id', $deTai->id)
            ->first();
            
        // Nếu không có phân công riêng, kiểm tra phân công hội đồng
        if (!$phanCongVaiTro) {
            $phanCongVaiTro = \App\Models\PhanCongVaiTro::with(['hoiDong.chiTietBaoCaos.deTai'])
                ->where('tai_khoan_id', $giangVienId)
                ->whereNull('de_tai_id')
                ->whereHas('hoiDong.chiTietBaoCaos.deTai', function($query) use ($deTai) {
                    $query->where('de_tais.id', $deTai->id);
                })
                ->first();
        }

        if (!$phanCongVaiTro) {
            return redirect()->route('giangvien.bang-diem.index')->with('error', 'Bạn chưa được phân công chấm đề tài này.');
        }

        // Bỏ điều kiện kiểm tra GVHD và GVPB phải đồng ý để cho phép chấm điểm
        // $hoiDong = $phanCongVaiTro->hoiDong;
        // $phanCongVaiTros = $hoiDong->phanCongVaiTros ?? collect();
        // $gvhdDongY = $phanCongVaiTros->where('loai_giang_vien', 'Giảng Viên Hướng Dẫn')->where('trang_thai', 'đồng ý')->count() > 0;
        // $gvpbDongY = $phanCongVaiTros->where('loai_giang_vien', 'Giảng Viên Phản Biện')->where('trang_thai', 'đồng ý')->count() > 0;
        // if (!($gvhdDongY && $gvpbDongY)) {
        //     return redirect()->route('giangvien.bang-diem.index')->with('error', 'Chỉ được chấm khi GVHD và GVPB đã đồng ý.');
        // }

        // Xác định có đợt báo cáo hay không
        $hasDotBaoCao = $bangDiem->dot_bao_cao_id !== null;
        $vaiTroCham = $phanCongVaiTro->vaiTro->ten ?? $phanCongVaiTro->loai_giang_vien;
        $loaiGiangVien = $phanCongVaiTro->loai_giang_vien;
        $vaiTro = $phanCongVaiTro->vaiTro->ten ?? null;
        
        // Logic mới: Chỉ cho phép chấm điểm báo cáo nếu có loại giảng viên là hướng dẫn hoặc phản biện
        $canGradeBaoCao = in_array($loaiGiangVien, ['Giảng Viên Hướng Dẫn', 'Giảng Viên Phản Biện']);
        
        // Cho phép chấm điểm thuyết trình, demo, câu hỏi, cộng nếu có loại giảng viên hoặc là vai trò khác
        $canGradeOtherScores = !is_null($loaiGiangVien) || in_array($vaiTro, ['Trưởng tiểu ban', 'Thư ký', 'Thành viên']);
        
        // Logic cũ để tương thích với view hiện tại
        $canEditBaoCaoAndThuyetTrinh = $canGradeOtherScores;
        $canGradeBaoCaoAndThuyetTrinh = $canGradeOtherScores;

        return view('giangvien.bang-diem.edit', compact(
            'bangDiem',
            'hasDotBaoCao',
            'vaiTroCham',
            'loaiGiangVien',
            'canGradeBaoCao',
            'canGradeOtherScores',
            'canEditBaoCaoAndThuyetTrinh',
            'canGradeBaoCaoAndThuyetTrinh'
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
        
        // Kiểm tra phân công riêng trước
        $phanCongVaiTro = \App\Models\PhanCongVaiTro::with(['deTai.nhom.sinhViens'])
            ->where('tai_khoan_id', $giangVienId)
            ->where('de_tai_id', $deTai->id)
            ->first();
            
        // Nếu không có phân công riêng, kiểm tra phân công hội đồng
        if (!$phanCongVaiTro) {
            $phanCongVaiTro = \App\Models\PhanCongVaiTro::with(['hoiDong.chiTietBaoCaos.deTai'])
                ->where('tai_khoan_id', $giangVienId)
                ->whereNull('de_tai_id')
                ->whereHas('hoiDong.chiTietBaoCaos.deTai', function($query) use ($deTai) {
                    $query->where('de_tais.id', $deTai->id);
                })
                ->first();
        }

        if (!$phanCongVaiTro) {
            return redirect()->route('giangvien.bang-diem.index')->with('error', 'Bạn chưa được phân công chấm đề tài này.');
        }

        // Kiểm tra quyền chấm điểm báo cáo
        $loaiGiangVien = $phanCongVaiTro->loai_giang_vien;
        $vaiTro = $phanCongVaiTro->vaiTro->ten ?? null;
        $canGradeBaoCao = in_array($loaiGiangVien, ['Giảng Viên Hướng Dẫn', 'Giảng Viên Phản Biện']);
        $canGradeOtherScores = !is_null($loaiGiangVien) || in_array($vaiTro, ['Trưởng tiểu ban', 'Thư ký', 'Thành viên']);

        $rules = [];
        $data = [];

        // Điểm báo cáo
        if ($canGradeBaoCao) {
            $rules = array_merge($rules, [
                'diem_bao_cao' => 'nullable|numeric|min:0|max:10',
            ]);
        }
        
        // Điểm thuyết trình và các điểm khác
        if ($canGradeOtherScores) {
            $rules = array_merge($rules, [
                'diem_thuyet_trinh' => 'nullable|numeric|min:0|max:3',
                'diem_demo' => 'nullable|numeric|min:0|max:4',
                'diem_cau_hoi' => 'nullable|numeric|min:0|max:1',
                'diem_cong' => 'nullable|numeric|min:0|max:1',
            ]);
        } else {
            return redirect()->route('giangvien.bang-diem.index')->with('error', 'Bạn không có quyền chấm điểm.');
        }

        $validated = $request->validate($rules);

        try {
            // Lưu điểm theo quyền chấm
            if ($canGradeBaoCao) {
                $data['diem_bao_cao'] = $request->diem_bao_cao;
            }
            
            if ($canGradeOtherScores) {
                $data['diem_thuyet_trinh'] = $request->diem_thuyet_trinh;
                $data['diem_demo'] = $request->diem_demo;
                $data['diem_cau_hoi'] = $request->diem_cau_hoi;
                $data['diem_cong'] = $request->diem_cong ?? 0;
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


        
    // Thêm bên trong class BangDiemController
    private function getVaiTroCham($phanCongVaiTro)
    {
        if (
            ($phanCongVaiTro->vaiTro && $phanCongVaiTro->vaiTro->ten === 'Thành viên') &&
            in_array($phanCongVaiTro->loai_giang_vien, ['Giảng Viên Hướng Dẫn', 'Giảng Viên Phản Biện'])
        ) {
            return $phanCongVaiTro->loai_giang_vien === 'Giảng Viên Hướng Dẫn' ? 'Hướng dẫn' : 'Phản biện';
        }
        return $phanCongVaiTro->vaiTro->ten ?? $phanCongVaiTro->loai_giang_vien;
    }
}
