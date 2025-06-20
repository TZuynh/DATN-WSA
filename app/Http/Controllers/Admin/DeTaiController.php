<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeTai;
use App\Models\Nhom;
use App\Models\TaiKhoan;
use App\Models\DotBaoCao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

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
        $request->validate([
            'ten_de_tai' => 'required|string',
            'mo_ta' => 'nullable|string',
            'y_kien_giang_vien' => 'nullable|string',
            'dot_bao_cao_id' => 'required|exists:dot_bao_caos,id',
            'nhom_id' => 'nullable|exists:nhoms,id',
            'giang_vien_id' => 'required|exists:tai_khoans,id'
        ]);

        try {
            $data = [
                'ten_de_tai' => $request->ten_de_tai,
                'mo_ta' => $request->mo_ta,
                'y_kien_giang_vien' => $request->y_kien_giang_vien,
                'dot_bao_cao_id' => $request->dot_bao_cao_id,
                'nhom_id' => $request->nhom_id,
                'giang_vien_id' => $request->giang_vien_id
            ];

            DeTai::create($data);
            return redirect()->route('admin.de-tai.index')->with('success', 'Thêm đề tài thành công');
        } catch (\Exception $e) {
            Log::error('Error creating de tai: ' . $e->getMessage());
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
        return view('admin.de-tai.edit', compact('deTai', 'nhoms', 'giangViens', 'dotBaoCaos'));
    }

    public function update(Request $request, DeTai $deTai)
    {
        $request->validate([
            'ten_de_tai' => 'required|string',
        ], [
            'ten_de_tai.required' => 'Vui lòng nhập tên đề tài',
        ]);

        try {
            $deTai->update([
                'ten_de_tai' => $request->ten_de_tai,
            ]);

            return redirect()->route('admin.de-tai.index')
                ->with('success', 'Cập nhật đề tài thành công');
        } catch (\Exception $e) {
            Log::error('Error updating de tai: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi cập nhật đề tài');
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
}