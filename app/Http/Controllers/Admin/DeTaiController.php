<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeTai;
use App\Models\Nhom;
use App\Models\TaiKhoan;
use App\Models\DotBaoCao;
use App\Models\ChiTietDeTaiBaoCao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DeTaiController extends Controller
{
    public function index()
    {
        try {
            $deTais = DeTai::with(['nhom.sinhViens', 'giangVien', 'dotBaoCao.hocKy'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);
            return view('admin.de-tai.index', compact('deTais'));
        } catch (\Exception $e) {
            Log::error('Error loading đề tài list: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi tải danh sách đề tài');
        }
    }

    public function create()
    {
        $nhoms = Nhom::all();
        $giangViens = TaiKhoan::where('vai_tro', 'giang_vien')->get();
        $dotBaoCaos = DotBaoCao::whereIn('trang_thai', ['chua_bat_dau', 'dang_dien_ra'])->get();
        return view('admin.de-tai.create', compact('nhoms', 'giangViens', 'dotBaoCaos'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'ten_de_tai' => 'required|string',
                'mo_ta' => 'nullable|string',
                'y_kien_giang_vien' => 'nullable|string',
                'dot_bao_cao_id' => 'required|exists:dot_bao_caos,id',
                'nhom_id' => 'nullable|exists:nhoms,id',
                'giang_vien_id' => 'nullable|exists:tai_khoans,id',
                'hoi_dong_id' => 'nullable|exists:hoi_dongs,id'
            ]);
        } catch (ValidationException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }

        try {
            DB::beginTransaction();

            $data = [
                'ten_de_tai' => $request->ten_de_tai,
                'mo_ta' => $request->mo_ta,
                'y_kien_giang_vien' => $request->y_kien_giang_vien,
                'dot_bao_cao_id' => $request->dot_bao_cao_id,
                'nhom_id' => $request->nhom_id,
                'giang_vien_id' => $request->giang_vien_id,
                'trang_thai' => DeTai::TRANG_THAI_CHO_DUYET
            ];

            $deTai = DeTai::create($data);

            // Nếu có hội đồng được chọn, tạo chi tiết báo cáo
            if ($request->filled('hoi_dong_id')) {
                ChiTietDeTaiBaoCao::create([
                    'hoi_dong_id' => $request->hoi_dong_id,
                    'de_tai_id' => $deTai->id,
                    'dot_bao_cao_id' => $request->dot_bao_cao_id,
                    'trang_thai' => 0 // Trạng thái mặc định là chờ duyệt
                ]);
            }

            // Cập nhật nhóm nếu có
            if ($request->filled('nhom_id')) {
                $nhom = Nhom::find($request->nhom_id);
                if ($nhom) {
                    $nhom->update(['de_tai_id' => $deTai->id]);
                }
            }

            DB::commit();

            // Kiểm tra nếu request có header X-Requested-With thì trả về JSON
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Thêm đề tài thành công',
                    'data' => $deTai
                ]);
            }

            return redirect()->route('admin.de-tai.index')->with('success', 'Thêm đề tài thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating de tai: ' . $e->getMessage());
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra khi thêm đề tài: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Có lỗi xảy ra khi thêm đề tài');
        }
    }

    public function edit(DeTai $deTai)
    {
        $nhoms = Nhom::all();
        $giangViens = TaiKhoan::where('vai_tro', 'giang_vien')->get();
        $dotBaoCaos = DotBaoCao::whereIn('trang_thai', ['chua_bat_dau', 'dang_dien_ra'])
            ->orWhere('id', $deTai->dot_bao_cao_id)
            ->get();

        // Lấy thông tin hội đồng của đề tài
        $hoiDong = null;
        $giangVienHoiDong = collect();
        $giangVienPhanBien = collect();
        
        if ($deTai->chiTietBaoCao && $deTai->chiTietBaoCao->hoiDong) {
            $hoiDong = $deTai->chiTietBaoCao->hoiDong;
            
            // Lấy danh sách giảng viên trong hội đồng
            $giangVienHoiDong = $hoiDong->phanCongVaiTros()
                ->with('taiKhoan')
                ->get()
                ->pluck('taiKhoan')
                ->filter();
            
            // Lấy danh sách giảng viên phản biện của đề tài
            $giangVienPhanBien = $hoiDong->phanCongVaiTros()
                ->where('loai_giang_vien', 'Giảng Viên Phản Biện')
                ->with('taiKhoan')
                ->get()
                ->pluck('taiKhoan')
                ->filter();
        }

        // Lấy danh sách sinh viên chưa có đề tài
        $sinhVienChuaCoDeTai = \App\Models\SinhVien::whereDoesntHave('nhoms.deTai')
            ->with('lop')
            ->get();

        return view('admin.de-tai.edit', compact(
            'deTai', 
            'nhoms', 
            'giangViens', 
            'dotBaoCaos',
            'hoiDong',
            'giangVienHoiDong',
            'giangVienPhanBien',
            'sinhVienChuaCoDeTai'
        ));
    }

    public function update(Request $request, DeTai $deTai)
    {
        $request->validate([
            'ten_de_tai' => 'required|string',
            'giang_vien_id' => 'nullable|exists:tai_khoans,id',
            'nhom_id' => 'nullable|exists:nhoms,id',
            'sinh_vien_ids' => 'nullable|array',
            'sinh_vien_ids.*' => 'exists:sinh_viens,id',
        ], [
            'ten_de_tai.required' => 'Vui lòng nhập tên đề tài',
            'giang_vien_id.exists' => 'Giảng viên không tồn tại',
            'nhom_id.exists' => 'Nhóm không tồn tại',
            'sinh_vien_ids.*.exists' => 'Sinh viên không tồn tại',
        ]);

        try {
            DB::beginTransaction();

            // Cập nhật thông tin cơ bản
            $deTai->update([
                'ten_de_tai' => $request->ten_de_tai,
            ]);

            // Phân công giảng viên hướng dẫn nếu có
            if ($request->filled('giang_vien_id')) {
                $deTai->update(['giang_vien_id' => $request->giang_vien_id]);
                
                // Đảm bảo giảng viên hướng dẫn được thêm vào hội đồng
                if ($deTai->chiTietBaoCao && $deTai->chiTietBaoCao->hoi_dong_id) {
                    $phanCongGVHD = \App\Models\PhanCongVaiTro::where('hoi_dong_id', $deTai->chiTietBaoCao->hoi_dong_id)
                        ->where('tai_khoan_id', $request->giang_vien_id)
                        ->first();

                    if ($phanCongGVHD) {
                        // Nếu đã có phân công, chỉ cập nhật loai_giang_vien
                        $phanCongGVHD->update(['loai_giang_vien' => 'Giảng Viên Hướng Dẫn']);
                    } else {
                        // Nếu chưa có phân công, tạo mới với vai trò "Thành viên"
                        $vaiTro = \App\Models\VaiTro::firstOrCreate(
                            ['ten' => 'Thành viên'],
                            ['mo_ta' => 'Thành viên hội đồng']
                        );
                        \App\Models\PhanCongVaiTro::create([
                            'hoi_dong_id' => $deTai->chiTietBaoCao->hoi_dong_id,
                            'tai_khoan_id' => $request->giang_vien_id,
                            'vai_tro_id' => $vaiTro->id,
                            'loai_giang_vien' => 'Giảng Viên Hướng Dẫn'
                        ]);
                    }
                }
            }

            // Phân công nhóm nếu có
            if ($request->filled('nhom_id')) {
                // Gỡ đề tài khỏi nhóm cũ
                if ($deTai->nhom_id) {
                    $nhomCu = \App\Models\Nhom::find($deTai->nhom_id);
                    if ($nhomCu) {
                        $nhomCu->update(['de_tai_id' => null]);
                    }
                }

                // Gán đề tài cho nhóm mới
                $deTai->update(['nhom_id' => $request->nhom_id]);
                $nhomMoi = \App\Models\Nhom::find($request->nhom_id);
                if ($nhomMoi) {
                    $nhomMoi->update(['de_tai_id' => $deTai->id]);
                }
            }

            // Phân công sinh viên nếu có
            if ($request->filled('sinh_vien_ids')) {
                // Tạo nhóm mới nếu chưa có nhóm
                if (!$deTai->nhom_id) {
                    $nhom = \App\Models\Nhom::create([
                        'ma_nhom' => 'N' . time(),
                        'ten' => 'Nhóm đề tài ' . $deTai->ten_de_tai,
                        'de_tai_id' => $deTai->id
                    ]);
                    $deTai->update(['nhom_id' => $nhom->id]);
                }

                // Cập nhật sinh viên cho nhóm
                $nhom = \App\Models\Nhom::find($deTai->nhom_id);
                if ($nhom) {
                    // Gỡ tất cả sinh viên khỏi nhóm cũ
                    $nhom->sinhViens()->detach();
                    
                    // Thêm sinh viên mới vào nhóm
                    foreach ($request->sinh_vien_ids as $sinhVienId) {
                        $nhom->sinhViens()->attach($sinhVienId);
                    }
                }
            }

            DB::commit();
            return redirect()->route('admin.de-tai.index')
                ->with('success', 'Cập nhật đề tài thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating de tai: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi cập nhật đề tài: ' . $e->getMessage());
        }
    }

    public function destroy(DeTai $deTai)
    {
        try {
            $deTai->delete();
            return redirect()->route('admin.de-tai.index')
                ->with('success', 'Xóa đề tài thành công');
        } catch (\Exception $e) {
            Log::error('Error deleting de tai: ' . $e->getMessage());
            return redirect()->route('admin.de-tai.index')
                ->with('error', 'Có lỗi xảy ra khi xóa đề tài');
        }
    }

    public function exportPdfDetail(DeTai $deTai)
    {
        // Load dữ liệu liên quan
        $deTai->load('nhom.sinhViens.lop', 'giangVien', 'dotBaoCao');

        // Tạo PDF
        $pdf = PDF::loadView('admin.de-tai.detail-pdf', compact('deTai'));
        
        // Cấu hình PDF
        $pdf->setPaper('a4', 'landscape');
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->setOption('defaultFont', 'DejaVu Sans');

        // Trả về file PDF để tải xuống
        return $pdf->download('PhieuDangKyDATN'.'.pdf');
    }

    public function previewPdfDetail(DeTai $deTai)
    {
        // Load dữ liệu liên quan
        $deTai->load('nhom.sinhViens.lop', 'giangVien', 'dotBaoCao');

        // Trả về view trực tiếp
        return view('admin.de-tai.detail-pdf', compact('deTai'));
    }

    public function exportWordDetail(DeTai $deTai)
    {
        // Load dữ liệu liên quan
        $deTai->load('nhom.sinhViens.lop', 'giangVien', 'dotBaoCao');

        // Tạo nội dung HTML
        $html = '
        <html>
        <head>
            <meta charset="utf-8">
            <style>
                body { font-family: "Times New Roman", serif; }
                table { width: 100%; border-collapse: collapse; }
                td { padding: 5px; }
                .header { text-align: center; margin-bottom: 20px; }
                .title { font-size: 18.5px; font-weight: bold; text-transform: uppercase; }
                .subtitle { font-size: 17px; }
                .section { margin-bottom: 15px; font-size: 17px; }
                .signature { margin-top: 50px; }
                .signature td { text-align: center; font-size: 17px; }
            </style>
        </head>
        <body>
            <div class="header">
                <table>
                    <tr>
                        <td>Trường CĐ Kỹ Thuật Cao Thắng</td>
                        <td style="text-align: center;">Cộng hòa xã hội chủ nghĩa Việt Nam</td>
                    </tr>
                    <tr>
                        <td>Khoa Công Nghệ Thông Tin</td>
                        <td style="text-align: center;">Độc lập - Tự do - Hạnh phúc</td>
                    </tr>
                </table>
                <div class="title">ĐĂNG KÝ ĐỀ TÀI TỐT NGHIỆP</div>
                <div class="subtitle">Niên khóa: 2022 - 2025</div>
            </div>

            <div class="section">
                <div class="section-title">GIẢNG VIÊN HƯỚNG DẪN:<span style="font-weight: bold; text-transform: uppercase;">' . ($deTai->giangVien->ten ?? 'Chưa có giảng viên') . '</span></div>
            </div>

            <div class="section">
                <div class="section-title">SINH VIÊN THỰC HIỆN:</div>';
                if ($deTai->nhom && $deTai->nhom->sinhViens->count() > 0) {
                    foreach ($deTai->nhom->sinhViens as $index => $sinhVien) {
                        $html .= '<div style="font-weight: bold; text-transform: uppercase;">' . ($index + 1) . '. ' . $sinhVien->ten . 
                                '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' .
                                '<span style="font-weight: normal;">MSSV: ' . $sinhVien->mssv . 
                                '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' .
                                '<span style="font-weight: normal;">Lớp: ' . ($sinhVien->lop->ten_lop ?? '') . '</span></div>';
                    }
                } else {
                    $html .= '<div style="font-weight: bold;">Chưa có sinh viên thực hiện</div>';
                }
        $html .= '</div>

            <div class="section">
                <div class="section-title">TÊN ĐỀ TÀI:<span style="font-weight: bold; text-transform: uppercase;">' . $deTai->ten_de_tai . '</span></div>
            </div>

            <div class="section">
                <div class="section-title">NỘI DUNG YÊU CẦU CỦA ĐỀ TÀI:</div>
                <div style="font-style: italic;">' . ($deTai->mo_ta ?? 'Chưa có nội dung yêu cầu') . '</div>
            </div>

            <div class="section">
                <div class="section-title">Thời gian thực hiện đề tài: từ ngày <span style="font-weight: bold;">' . (optional($deTai->dotBaoCao)->ngay_bat_dau ? $deTai->dotBaoCao->ngay_bat_dau->format('d/m/Y') : 'N/A') . '</span> đến ngày <span style="font-weight: bold;">' . (optional($deTai->dotBaoCao)->ngay_ket_thuc ? $deTai->dotBaoCao->ngay_ket_thuc->format('d/m/Y') : 'N/A') . '</span></div>
            </div>

            <div class="section">
                <div class="section-title">Ý KIẾN CỦA GIẢNG VIÊN HƯỚNG DẪN:</div>
                <div style="font-style: italic;">' . ($deTai->y_kien_giang_vien ?? 'Chưa có ý kiến') . '</div>
            </div>

            <div class="signature">
                <table>
                    <tr>
                        <td>Giám Hiệu</td>
                        <td>Khoa Công Nghệ Thông Tin</td>
                        <td>GV Hướng dẫn</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td>(Ký và ghi rõ họ tên)</td>
                    </tr>
                </table>
            </div>
        </body>
        </html>';

        // Tạo file Word
        $fileName = 'PhieuDangKyDATN'.'.doc';

        // Trả về file để tải xuống
        return response($html, 200, [
            'Content-Type' => 'application/msword',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Content-Length' => strlen($html)
        ]);
    }

    public function approve($id)
    {
        $deTai = DeTai::findOrFail($id);
        $deTai->trang_thai = \App\Models\DeTai::TRANG_THAI_DANG_THUC_HIEN_GVHD; // = 1
        $deTai->save();
        return redirect()->route('admin.de-tai.index')->with('success', 'Đề tài đã được duyệt!');
    }
}