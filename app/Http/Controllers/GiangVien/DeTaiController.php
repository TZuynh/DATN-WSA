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
use App\Models\PhanCongCham;
use App\Models\ChiTietDeTaiBaoCao;
use App\Models\VaiTro;

class DeTaiController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        // Kiểm tra theo loai_giang_vien
        $isPhanBien = \App\Models\PhanCongVaiTro::where('tai_khoan_id', $user->id)
            ->where('loai_giang_vien', 'Giảng Viên Phản Biện')
            ->exists();
        $isThuKy = \App\Models\PhanCongVaiTro::where('tai_khoan_id', $user->id)
            ->whereHas('vaiTro', function($q) { $q->where('ten', 'Thư ký'); })
            ->exists();

        if ($isPhanBien) {
            // Lấy các đề tài mà giảng viên được phân công phản biện
            $deTais = \App\Models\DeTai::with(['nhoms.sinhViens', 'dotBaoCao.hocKy', 'giangVien'])
                ->whereHas('chiTietBaoCao.hoiDong.phanCongVaiTros', function($query) use ($user) {
                    $query->where('tai_khoan_id', $user->id)
                        ->where('loai_giang_vien', 'Giảng Viên Phản Biện');
                })
                ->whereNotIn('trang_thai', [2, 4]) // Chỉ lấy đề tài chưa được duyệt hoặc từ chối
                ->orderBy('created_at', 'desc')
                ->get();
        } elseif ($isThuKy) {
            // Lấy tất cả đề tài thuộc hội đồng mà user là Thư ký
            $hoiDongIds = \App\Models\PhanCongVaiTro::where('tai_khoan_id', $user->id)
                ->whereHas('vaiTro', function($q) { $q->where('ten', 'Thư ký'); })
                ->pluck('hoi_dong_id');
            $deTaiIds = \App\Models\ChiTietDeTaiBaoCao::whereIn('hoi_dong_id', $hoiDongIds)->pluck('de_tai_id');
            $deTais = \App\Models\DeTai::with(['nhoms.sinhViens', 'dotBaoCao.hocKy', 'giangVien'])
                ->whereIn('id', $deTaiIds)
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            // Nếu là giảng viên hướng dẫn, lấy các đề tài do họ hướng dẫn
            $giangVienId = $user->id;
            $deTais = \App\Models\DeTai::with(['nhoms.sinhViens', 'dotBaoCao.hocKy'])
                ->where('giang_vien_id', $giangVienId)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('giangvien.de-tai.index', compact('deTais', 'isPhanBien', 'isThuKy'));
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
                'nhom_id' => $request->nhom_id,
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

            $chiTiet = ChiTietDeTaiBaoCao::where('de_tai_id', $deTai->id)->first();
            if (!$chiTiet) {
                // Bạn cần xác định hội đồng nào sẽ nhận đề tài này (ví dụ lấy theo đợt báo cáo hoặc logic riêng)
                // Ở đây ví dụ lấy hội đồng đầu tiên của đợt báo cáo
                $hoiDong = \App\Models\HoiDong::where('dot_bao_cao_id', $deTai->dot_bao_cao_id)->first();
                if ($hoiDong) {
                    $chiTiet = ChiTietDeTaiBaoCao::create([
                        'dot_bao_cao_id' => $deTai->dot_bao_cao_id,
                        'de_tai_id' => $deTai->id,
                        'hoi_dong_id' => $hoiDong->id,
                    ]);
                } else {
                    // Nếu không có hội đồng, có thể báo lỗi hoặc xử lý logic khác
                    return redirect()->back()->with('error', 'Không tìm thấy hội đồng phù hợp cho đề tài này.');
                }
            }
            // Tự động phân công giảng viên hướng dẫn vào hội đồng
            if ($deTai->giang_vien_id && $chiTiet && $chiTiet->hoi_dong_id) {
                $phanCongGVHD = \App\Models\PhanCongVaiTro::where('hoi_dong_id', $chiTiet->hoi_dong_id)
                    ->where('tai_khoan_id', $deTai->giang_vien_id)
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
                        'hoi_dong_id' => $chiTiet->hoi_dong_id,
                        'tai_khoan_id' => $deTai->giang_vien_id,
                        'vai_tro_id' => $vaiTro->id,
                        'loai_giang_vien' => 'Giảng Viên Hướng Dẫn'
                    ]);
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
            'trang_thai' => 'nullable|integer|in:0,1,2,3,4'
        ]);

        try {
            $deTai->update([
                'ten_de_tai' => $request->ten_de_tai,
                'mo_ta' => $request->mo_ta,
                'nhom_id' => $request->nhom_id,
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
            // Xóa các bản ghi liên quan trước
            \App\Models\PhanCongCham::where('de_tai_id', $deTai->id)->delete();
            \App\Models\ChiTietDeTaiBaoCao::where('de_tai_id', $deTai->id)->delete();
            \App\Models\LichCham::where('de_tai_id', $deTai->id)->delete();

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
        
        // Kiểm tra quyền phản biện
        $isPhanBien = \App\Models\PhanCongVaiTro::where('tai_khoan_id', $user->id)
            ->where('loai_giang_vien', 'Giảng Viên Phản Biện')
            ->exists();
        if (!$isPhanBien) {
            abort(403, 'Bạn không có quyền thực hiện thao tác này.');
        }

        // Cập nhật trạng thái đề tài
        if ($request->action === 'approve') {
            $deTai->trang_thai = 2;
            $msg = 'Đề tài đã được giảng viên phản biện duyệt.';
        } else {
            $deTai->trang_thai = 4;
            $msg = 'Đề tài đã bị giảng viên phản biện từ chối.';
        }
        $deTai->save();

        // Tìm đúng hội đồng mà giảng viên phản biện đang thuộc về
        $phanBien = \App\Models\PhanCongVaiTro::where('tai_khoan_id', $user->id)
            ->where('loai_giang_vien', 'Giảng Viên Phản Biện')
            ->whereHas('hoiDong', function($q) use ($deTai) {
                $q->where('dot_bao_cao_id', $deTai->dot_bao_cao_id);
            })
            ->first();

        if ($phanBien && $phanBien->hoi_dong_id) {
            $hoiDongId = $phanBien->hoi_dong_id;
        } else {
            $hoiDong = \App\Models\HoiDong::where('dot_bao_cao_id', $deTai->dot_bao_cao_id)->first();
            $hoiDongId = $hoiDong ? $hoiDong->id : null;
        }

        if ($hoiDongId) {
            $chiTiet = \App\Models\ChiTietDeTaiBaoCao::updateOrCreate(
                ['de_tai_id' => $deTai->id],
                [
                    'dot_bao_cao_id' => $deTai->dot_bao_cao_id,
                    'hoi_dong_id' => $hoiDongId,
                ]
            );

            // Đảm bảo có bản ghi Giảng Viên Phản Biện
            // Chỉ cập nhật loai_giang_vien, không thay đổi vai_tro_id
            $phanCongPhanBien = \App\Models\PhanCongVaiTro::where('hoi_dong_id', $chiTiet->hoi_dong_id)
                ->where('tai_khoan_id', $user->id)
                ->first();

            if ($phanCongPhanBien) {
                // Nếu đã có phân công, chỉ cập nhật loai_giang_vien
                $phanCongPhanBien->update(['loai_giang_vien' => 'Giảng Viên Phản Biện']);
            } else {
                // Nếu chưa có phân công, tạo mới với vai trò "Thành viên"
                $vaiTro = \App\Models\VaiTro::firstOrCreate(
                    ['ten' => 'Thành viên'],
                    ['mo_ta' => 'Thành viên hội đồng']
                );
                \App\Models\PhanCongVaiTro::create([
                    'hoi_dong_id' => $chiTiet->hoi_dong_id,
                    'tai_khoan_id' => $user->id,
                    'vai_tro_id' => $vaiTro->id,
                    'loai_giang_vien' => 'Giảng Viên Phản Biện'
                ]);
            }

            // Đảm bảo có bản ghi Giảng Viên Hướng Dẫn
            if ($deTai->giang_vien_id) {
                $phanCongGVHD = \App\Models\PhanCongVaiTro::where('hoi_dong_id', $chiTiet->hoi_dong_id)
                    ->where('tai_khoan_id', $deTai->giang_vien_id)
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
                        'hoi_dong_id' => $chiTiet->hoi_dong_id,
                        'tai_khoan_id' => $deTai->giang_vien_id,
                        'vai_tro_id' => $vaiTro->id,
                        'loai_giang_vien' => 'Giảng Viên Hướng Dẫn'
                    ]);
                }
            }

            // Tạo bản ghi phân công chấm nếu chưa có
            \App\Models\PhanCongCham::firstOrCreate([
                'de_tai_id' => $deTai->id,
                'hoi_dong_id' => $chiTiet->hoi_dong_id,
            ], [
                'lich_cham' => now(),
            ]);
        }

        return redirect()->route('giangvien.de-tai.index')->with('success', $msg);
    }
} 