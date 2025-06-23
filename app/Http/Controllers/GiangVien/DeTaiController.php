<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use App\Models\DeTai;
use App\Models\Nhom;
use App\Models\DotBaoCao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\SimpleType\Jc;

class DeTaiController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        // Kiểm tra theo loai_giang_vien
        $isPhanBien = \App\Models\PhanCongVaiTro::where('tai_khoan_id', $user->id ?? 0)
            ->where('loai_giang_vien', 'Giảng Viên Phản Biện')
            ->exists();

        if ($isPhanBien) {
            // Lấy các hội đồng mà user là phản biện
            $hoiDongIds = \App\Models\PhanCongVaiTro::where('tai_khoan_id', $user->id)
                ->where('loai_giang_vien', 'Giảng Viên Phản Biện')
                ->pluck('hoi_dong_id');
            // Lấy các giảng viên hướng dẫn trong các hội đồng đó
            $giangVienHuongDanIds = \App\Models\PhanCongVaiTro::whereIn('hoi_dong_id', $hoiDongIds)
                ->where('loai_giang_vien', 'Giảng Viên Hướng Dẫn')
                ->pluck('tai_khoan_id');
            // Lấy tất cả đề tài của các giảng viên hướng dẫn này (không lọc trạng thái)
            $deTais = \App\Models\DeTai::with(['nhoms.sinhViens', 'dotBaoCao.hocKy', 'giangVien'])
                ->whereIn('giang_vien_id', $giangVienHuongDanIds)
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $giangVienId = $user->id;
            $deTais = \App\Models\DeTai::with(['nhoms.sinhViens', 'dotBaoCao.hocKy'])
                ->where('giang_vien_id', $giangVienId)
                ->orderBy('created_at', 'desc')
                ->get();
        }
        return view('giangvien.de-tai.index', compact('deTais', 'isPhanBien'));
    }

    public function create()
    {
        $nhoms = Nhom::whereDoesntHave('deTai')->get();
        $dotBaoCaos = DotBaoCao::whereIn('trang_thai', ['chua_bat_dau', 'dang_dien_ra'])->get();
        return view('giangvien.de-tai.create', compact('nhoms', 'dotBaoCaos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ten_de_tai' => 'required|string',
            'mo_ta' => 'nullable|string',
            'y_kien_giang_vien' => 'nullable|string',
            'dot_bao_cao_id' => 'required|exists:dot_bao_caos,id',
            'nhom_id' => 'nullable|exists:nhoms,id',
        ]);

        try {
            $deTai = DeTai::create([
                'ten_de_tai' => $request->ten_de_tai,
                'mo_ta' => $request->mo_ta,
                'y_kien_giang_vien' => $request->y_kien_giang_vien,
                'dot_bao_cao_id' => $request->dot_bao_cao_id,
                'giang_vien_id' => auth()->id(),
            ]);

            if ($request->filled('nhom_id')) {
                $nhom = Nhom::find($request->nhom_id);
                if ($nhom) {
                    $nhom->de_tai_id = $deTai->id;
                    $nhom->save();
                }
            }

            return redirect()->route('giangvien.de-tai.index')->with('success', 'Thêm đề tài thành công');
        } catch (\Exception $e) {
            Log::error('Error creating de tai: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi thêm đề tài');
        }
    }

    public function edit(DeTai $deTai)
    {
        if ($deTai->giang_vien_id !== auth()->id()) {
            return redirect()->route('giangvien.de-tai.index')
                ->with('error', 'Bạn không có quyền chỉnh sửa đề tài này.');
        }

        // Chỉ lấy nhóm chưa có đề tài hoặc đang giữ đề tài này
        $nhoms = Nhom::whereNull('de_tai_id')
            ->orWhere('de_tai_id', $deTai->id)
            ->get();

        $dotBaoCaos = DotBaoCao::whereIn('trang_thai', ['chua_bat_dau', 'dang_dien_ra'])
            ->orWhere('id', $deTai->dot_bao_cao_id)
            ->get();
        $daPhanCongCham = $deTai->phanCongCham()->exists();
        $daCoLichCham = $deTai->lichCham()->exists();
        return view('giangvien.de-tai.edit', compact('deTai', 'nhoms', 'daPhanCongCham', 'daCoLichCham', 'dotBaoCaos'));
    }

    public function update(Request $request, DeTai $deTai)
    {
        if ($deTai->giang_vien_id !== auth()->id()) {
            return redirect()->route('giangvien.de-tai.index')
                ->with('error', 'Bạn không có quyền cập nhật đề tài này.');
        }

        $request->validate([
            'ten_de_tai' => 'required|string',
            'mo_ta' => 'nullable|string',
            'y_kien_giang_vien' => 'nullable|string',
            'dot_bao_cao_id' => 'required|exists:dot_bao_caos,id',
            'nhom_id' => 'nullable|exists:nhoms,id',
            'trang_thai' => 'required|integer|in:0,1,2,3,4'
        ]);

        try {
            $deTai->update([
                'ten_de_tai' => $request->ten_de_tai,
                'mo_ta' => $request->mo_ta,
                'y_kien_giang_vien' => $request->y_kien_giang_vien,
                'dot_bao_cao_id' => $request->dot_bao_cao_id,
                'trang_thai' => $request->trang_thai
            ]);

            // Gỡ đề tài khỏi các nhóm cũ
            Nhom::where('de_tai_id', $deTai->id)->update(['de_tai_id' => null]);

            // Gán đề tài cho nhóm mới (nếu có)
            if ($request->filled('nhom_id')) {
                $nhom = Nhom::find($request->nhom_id);
                if ($nhom) {
                    $nhom->de_tai_id = $deTai->id;
                    $nhom->save();
                }
            }

            return redirect()->route('giangvien.de-tai.index')
                ->with('success', 'Cập nhật đề tài thành công');
        } catch (\Exception $e) {
            Log::error('Error updating de tai: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi cập nhật đề tài');
        }
    }

    public function destroy(DeTai $deTai)
    {
        if ($deTai->giang_vien_id !== auth()->id()) {
            return redirect()->route('giangvien.de-tai.index')
                ->with('error', 'Bạn không có quyền xóa đề tài này.');
        }

        try {
            // Gỡ liên kết đề tài khỏi các nhóm trước khi xóa
            $deTai->nhoms()->update(['de_tai_id' => null]);

            $deTai->delete();
            return redirect()->route('giangvien.de-tai.index')
                ->with('success', 'Xóa đề tài thành công');
        } catch (\Exception $e) {
            Log::error('Error deleting de tai: ' . $e->getMessage());
            return redirect()->route('giangvien.de-tai.index')
                ->with('error', 'Có lỗi xảy ra khi xóa đề tài');
        }
    }

    public function updateTrangThai(Request $request, DeTai $deTai)
    {
        $request->validate([
            'trang_thai' => 'required|integer|min:0|max:4'
        ]);

        try {
            $deTai->update(['trang_thai' => $request->trang_thai]);
            return redirect()->back()->with('success', 'Cập nhật trạng thái đề tài thành công');
        } catch (\Exception $e) {
            Log::error('Lỗi khi cập nhật trạng thái đề tài: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi cập nhật trạng thái đề tài');
        }
    }

    public function exportPdfDetail(DeTai $deTai)
    {
        // Kiểm tra quyền xem chi tiết đề tài
        if ($deTai->giang_vien_id !== auth()->id()) {
            abort(403, 'Bạn không có quyền xem chi tiết đề tài này.');
        }

        // Load dữ liệu liên quan
        $deTai->load('nhoms.sinhViens.lop', 'giangVien', 'dotBaoCao');

        // Tạo PDF
        $pdf = PDF::loadView('giangvien.de-tai.detail-pdf', compact('deTai'));
        
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
        // Kiểm tra quyền xem chi tiết đề tài
        if ($deTai->giang_vien_id !== auth()->id()) {
            abort(403, 'Bạn không có quyền xem chi tiết đề tài này.');
        }

        // Load dữ liệu liên quan
        $deTai->load('nhoms.sinhViens.lop', 'giangVien', 'dotBaoCao');

        // Trả về view trực tiếp
        return view('giangvien.de-tai.detail-pdf', compact('deTai'));
    }

    public function exportWordDetail(DeTai $deTai)
    {
        // Kiểm tra quyền xem chi tiết đề tài
        if ($deTai->giang_vien_id !== auth()->id()) {
            abort(403, 'Bạn không có quyền xem chi tiết đề tài này.');
        }

        // Load dữ liệu liên quan
        $deTai->load('nhoms.sinhViens.lop', 'giangVien', 'dotBaoCao');

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
                if ($deTai->nhoms && $deTai->nhoms->first()->sinhViens->count() > 0) {
                    foreach ($deTai->nhoms->first()->sinhViens as $index => $sinhVien) {
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

    public function phanBienDuyet(Request $request, DeTai $deTai)
    {
        $user = auth()->user();
        $isPhanBien = \App\Models\PhanCongVaiTro::where('tai_khoan_id', $user->id)
            ->where('loai_giang_vien', 'Giảng Viên Phản Biện')
            ->exists();
        if (!$isPhanBien) {
            abort(403, 'Bạn không có quyền thực hiện thao tác này.');
        }
        if ($request->action === 'approve') {
            $deTai->trang_thai = 2; // 2 = Giảng viên phản biện đã duyệt
            $msg = 'Đề tài đã được giảng viên phản biện duyệt.';
        } else {
            $deTai->trang_thai = 4; // 4 = Giảng viên phản biện từ chối
            $msg = 'Đề tài đã bị giảng viên phản biện từ chối.';
        }
        $deTai->save();
        return redirect()->route('giangvien.de-tai.index')->with('success', $msg);
    }
} 