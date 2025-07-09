<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PhanCongCham;
use App\Models\DeTai;
use App\Models\ChiTietDeTaiBaoCao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\TaiKhoan;
use App\Models\PhanCongVaiTro;
use App\Models\HoiDong;
use App\Models\VaiTro;

class PhanCongChamController extends Controller
{
    public function index(Request $request)
    {
        $hoiDongId = $request->get('hoi_dong_id');
        $hoiDong = null;
        if ($hoiDongId) {
            $hoiDong = \App\Models\HoiDong::find($hoiDongId);
        }
        // Lấy tất cả đề tài đã được giảng viên phản biện duyệt (trang_thai = 2)
        $deTais = \App\Models\DeTai::with(['phanCongCham', 'dotBaoCao', 'giangVien', 'chiTietBaoCao.hoiDong.phanCongVaiTros.taiKhoan'])
            ->where('trang_thai', 2)
            ->orderBy('created_at', 'desc')
            ->get();

        // Lấy các phân công chấm hiện có (vẫn paginate như cũ)
        $phanCongChams = \App\Models\PhanCongCham::with([
            'deTai.nhoms',
            'deTai.lichCham',
            'deTai.dotBaoCao',
            'deTai.giangVien',
            'deTai.chiTietBaoCao.hoiDong.phanCongVaiTros.taiKhoan',
            'hoiDong',
            'hoiDong.phanCongVaiTros.taiKhoan',
        ])
        ->whereHas('deTai', function($query) {
            $query->where('trang_thai', 2);
        })
        ->latest()
        ->paginate(10);

        return view('admin.phan-cong-cham.index', compact('phanCongChams', 'deTais', 'hoiDong'));
    }

    public function create()
    {
        $deTais = DeTai::where('trang_thai', 2)
            ->whereDoesntHave('phanCongCham')
            ->whereHas('chiTietBaoCao.hoiDong')
            ->select('id', 'ma_de_tai', 'ten_de_tai')
            ->get();

        $giangViens = TaiKhoan::where('vai_tro', 'giang_vien')->select('id', 'ten')->get();

        if ($deTais->isEmpty()) {
            return redirect()->route('admin.phan-cong-cham.index')
                ->with('error', 'Không có đề tài nào đủ điều kiện để phân công chấm. Đề tài phải đã được giảng viên phản biện duyệt, chưa được phân công chấm và đã được gán vào một hội đồng.');
        }

        return view('admin.phan-cong-cham.create', compact('deTais', 'giangViens'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'de_tai_id' => 'required|exists:de_tais,id|unique:phan_cong_chams,de_tai_id',
            'lich_cham' => 'required|date',
        ], [
            'de_tai_id.required' => 'Vui lòng chọn đề tài.',
            'de_tai_id.unique' => 'Đề tài này đã được phân công chấm.',
            'lich_cham.required' => 'Vui lòng chọn lịch chấm.',
        ]);

        try {
            DB::beginTransaction();

            $chiTiet = ChiTietDeTaiBaoCao::where('de_tai_id', $validated['de_tai_id'])->first();
            if (!$chiTiet || !$chiTiet->hoi_dong_id) {
                return redirect()->back()->with('error', 'Không tìm thấy hội đồng cho đề tài này. Vui lòng kiểm tra lại phân công hội đồng.');
            }

            $phanCongCham = PhanCongCham::create([
                'de_tai_id' => $validated['de_tai_id'],
                'hoi_dong_id' => $chiTiet->hoi_dong_id,
                'lich_cham' => $request->input('lich_cham'),
            ]);

            // Tự động tạo lịch chấm nếu chưa có
            $deTai = \App\Models\DeTai::find($validated['de_tai_id']);
            $nhomId = $deTai ? $deTai->nhom_id : null;
            $dotBaoCaoId = $deTai ? $deTai->dot_bao_cao_id : null;
            $coLichCham = \App\Models\LichCham::where('de_tai_id', $deTai->id)->exists();
            if ($deTai && $nhomId && $dotBaoCaoId && !$coLichCham) {
                \App\Models\LichCham::create([
                    'hoi_dong_id' => $chiTiet->hoi_dong_id,
                    'dot_bao_cao_id' => $dotBaoCaoId,
                    'nhom_id' => $nhomId,
                    'de_tai_id' => $deTai->id,
                    'phan_cong_cham_id' => $phanCongCham->id,
                    'lich_tao' => $validated['lich_cham'],
                    'thu_tu' => 1,
                ]);
            }

            DB::commit();
            return redirect()->route('admin.phan-cong-cham.index')->with('success', 'Phân công chấm và lịch chấm đã được tạo thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $phanCongCham = PhanCongCham::with(['deTai', 'hoiDong'])->findOrFail($id);

        $deTais = DeTai::where('trang_thai', 2)
            ->whereHas('chiTietBaoCao.hoiDong')
            ->where(function($query) use ($phanCongCham) {
                $query->whereDoesntHave('phanCongCham')
                    ->orWhere('id', $phanCongCham->de_tai_id);
            })
            ->select('id', 'ma_de_tai', 'ten_de_tai')
            ->get();

        $deTaiCoLichCham = \App\Models\LichCham::where('de_tai_id', $phanCongCham->de_tai_id)->exists();

        $giangViens = TaiKhoan::where('vai_tro', 'giang_vien')->select('id', 'ten')->get();

        return view('admin.phan-cong-cham.edit', compact('phanCongCham', 'deTais', 'deTaiCoLichCham', 'giangViens'));
    }

    public function update(Request $request, $id)
    {
        $phanCongCham = PhanCongCham::findOrFail($id);

        $validated = $request->validate([
            'de_tai_id' => 'required|exists:de_tais,id|unique:phan_cong_chams,de_tai_id,' . $id,
            'lich_cham' => 'required|date_format:Y-m-d H:i',
        ], [
            'de_tai_id.required' => 'Vui lòng chọn đề tài.',
            'de_tai_id.unique' => 'Đề tài này đã được phân công chấm.',
            'lich_cham.required' => 'Vui lòng chọn lịch chấm.',
        ]);

        try {
            DB::beginTransaction();

            $chiTiet = ChiTietDeTaiBaoCao::where('de_tai_id', $validated['de_tai_id'])->first();
            if (!$chiTiet || !$chiTiet->hoi_dong_id) {
                return redirect()->back()->with('error', 'Không tìm thấy hội đồng cho đề tài được chọn.');
            }

            $phanCongCham->update([
                'de_tai_id' => $validated['de_tai_id'],
                'hoi_dong_id' => $chiTiet->hoi_dong_id,
                'lich_cham' => $validated['lich_cham'],
            ]);

            DB::commit();
            return redirect()->route('admin.phan-cong-cham.index')->with('success', 'Cập nhật phân công chấm thành công.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $phanCongCham = PhanCongCham::findOrFail($id);
            $phanCongCham->delete();
            return redirect()->route('admin.phan-cong-cham.index')
                ->with('success', 'Xóa phân công chấm thành công.');
        } catch (\Exception $e) {
            return redirect()->route('admin.phan-cong-cham.index')
                ->with('error', 'Xóa phân công chấm thất bại.');
        }
    }

    public function getHoiDongInfo(Request $request)
    {
        $request->validate(['de_tai_id' => 'required|exists:de_tais,id']);

        $chiTiet = ChiTietDeTaiBaoCao::with('hoiDong.phanCongVaiTros.taiKhoan', 'hoiDong.phanCongVaiTros.vaiTro')
            ->where('de_tai_id', $request->de_tai_id)
            ->first();

        if (!$chiTiet || !$chiTiet->hoiDong) {
            return response()->json(['error' => 'Không tìm thấy hội đồng cho đề tài này.'], 404);
        }

        $hoiDong = $chiTiet->hoiDong;

        $members = $hoiDong->phanCongVaiTros->map(function ($phanCong) {
            $vaiTro = $phanCong->vaiTro->ten ?? 'N/A';

            // Nếu là trưởng tiểu ban hoặc thư ký thì không có loại giảng viên
            if (in_array($vaiTro, ['Trưởng tiểu ban', 'Thư ký'])) {
                $loai = '';
            } else {
                $loai = in_array($phanCong->loai_giang_vien, ['Giảng Viên Hướng Dẫn', 'Giảng Viên Phản Biện', 'Giảng Viên Khác'])
                    ? $phanCong->loai_giang_vien
                    : 'N/A';
            }

            return [
                'ten' => $phanCong->taiKhoan->ten ?? 'N/A',
                'vai_tro' => $vaiTro,
                'loai_giang_vien' => $loai,
            ];
        });

        // Sắp xếp giảng viên
        $sortedMembers = $members->sortBy(function ($member) {
            switch ($member['loai_giang_vien']) {
                case 'Giảng Viên Hướng Dẫn': return 1;
                case 'Giảng Viên Phản Biện': return 2;
                case 'Giảng Viên Khác': return 3;
                default:
                    switch ($member['vai_tro']) {
                        case 'Trưởng tiểu ban': return -2;
                        case 'Thư ký': return -1;
                        default: return 4;
                    }
            }
        })->values();

        return response()->json([
            'ten_hoi_dong' => $hoiDong->ten,
            'members' => $sortedMembers
        ]);
    }

    // Hiển thị form phân công phản biện
    public function phanCongPhanBien(Request $request)
    {
        $hoiDongId = $request->get('hoi_dong_id');
        $hoiDong = null;
        if ($hoiDongId) {
            $hoiDong = \App\Models\HoiDong::find($hoiDongId);
        }
        // Lấy các đề tài đã được GVHD đồng ý (trang_thai = 1) và đã có hội đồng
        $deTais = DeTai::where('trang_thai', 1)
            ->whereHas('chiTietBaoCao.hoiDong')
            ->get();

        // Lấy danh sách tất cả giảng viên
        $giangViens = \App\Models\TaiKhoan::where('vai_tro', 'giang_vien')->get();
        
        return view('admin.phan-cong-cham.phan-bien', compact('deTais', 'giangViens', 'hoiDong'));
    }

    // AJAX: Lấy danh sách giảng viên có thể phản biện cho đề tài
    public function getGiangVienHoiDong($de_tai_id)
    {
        $chiTiet = \App\Models\ChiTietDeTaiBaoCao::where('de_tai_id', $de_tai_id)
            ->with('hoiDong.phanCongVaiTros.taiKhoan', 'hoiDong.phanCongVaiTros.vaiTro')
            ->first();

        if (!$chiTiet || !$chiTiet->hoiDong) {
            return response()->json([]);
        }

        $deTai = \App\Models\DeTai::find($de_tai_id);
        $giangVienHuongDanId = $deTai ? $deTai->giang_vien_id : null;

        $giangViens = $chiTiet->hoiDong->phanCongVaiTros
            ->filter(function($pc) use ($giangVienHuongDanId) {
                return $pc->tai_khoan_id != $giangVienHuongDanId;
            })
            ->unique('tai_khoan_id')
            ->map(function($pc) {
                return [
                    'id' => $pc->tai_khoan_id,
                    'ten' => optional($pc->taiKhoan)->ten,
                    'vai_tro' => optional($pc->vaiTro)->ten,
                ];
            })
            ->values();

        return response()->json($giangViens);
    }

    // Lưu phân công phản biện
    public function storePhanBien(Request $request)
    {
        $request->validate([
            'de_tai_id' => 'required|exists:de_tais,id',
            'giang_vien_id' => 'required|exists:tai_khoans,id',
        ]);

        DB::beginTransaction();
        try {
            // Tìm hoặc tạo chi tiết đề tài báo cáo
            $deTai = DeTai::findOrFail($request->de_tai_id);
            $chiTiet = ChiTietDeTaiBaoCao::firstOrCreate(
                ['de_tai_id' => $request->de_tai_id],
                [
                    'dot_bao_cao_id' => $deTai->dot_bao_cao_id,
                    'hoi_dong_id' => null
                ]
            );

            // Tìm hoặc tạo hội đồng nếu chưa có
            if (!$chiTiet->hoi_dong_id) {
                $hoiDong = \App\Models\HoiDong::firstOrCreate(
                    ['dot_bao_cao_id' => $deTai->dot_bao_cao_id],
                    ['ten' => 'Hội đồng đợt ' . $deTai->dot_bao_cao_id]
                );
                $chiTiet->hoi_dong_id = $hoiDong->id;
                $chiTiet->save();
            }

            // Cập nhật hoặc tạo phân công vai trò cho giảng viên phản biện
            // Chỉ cập nhật loai_giang_vien, không thay đổi vai_tro_id
            $phanCongPhanBien = PhanCongVaiTro::where('hoi_dong_id', $chiTiet->hoi_dong_id)
                ->where('tai_khoan_id', $request->giang_vien_id)
                ->first();

            if ($phanCongPhanBien) {
                // Nếu đã có phân công, chỉ cập nhật loai_giang_vien
                $phanCongPhanBien->update(['loai_giang_vien' => 'Giảng Viên Phản Biện']);
            } else {
                // Nếu chưa có phân công, tạo mới với vai trò "Thành viên"
                $vaiTro = VaiTro::firstOrCreate(
                    ['ten' => 'Thành viên'],
                    ['mo_ta' => 'Thành viên hội đồng']
                );
                PhanCongVaiTro::create([
                    'hoi_dong_id' => $chiTiet->hoi_dong_id,
                    'tai_khoan_id' => $request->giang_vien_id,
                    'vai_tro_id' => $vaiTro->id,
                    'loai_giang_vien' => 'Giảng Viên Phản Biện'
                ]);
            }

            // Đảm bảo giảng viên hướng dẫn cũng được thêm vào hội đồng
            if ($deTai->giang_vien_id) {
                $phanCongGVHD = PhanCongVaiTro::where('hoi_dong_id', $chiTiet->hoi_dong_id)
                    ->where('tai_khoan_id', $deTai->giang_vien_id)
                    ->first();

                if ($phanCongGVHD) {
                    // Nếu đã có phân công, chỉ cập nhật loai_giang_vien
                    $phanCongGVHD->update(['loai_giang_vien' => 'Giảng Viên Hướng Dẫn']);
                } else {
                    // Nếu chưa có phân công, tạo mới với vai trò "Thành viên"
                    $vaiTro = VaiTro::firstOrCreate(
                        ['ten' => 'Thành viên'],
                        ['mo_ta' => 'Thành viên hội đồng']
                    );
                    PhanCongVaiTro::create([
                        'hoi_dong_id' => $chiTiet->hoi_dong_id,
                        'tai_khoan_id' => $deTai->giang_vien_id,
                        'vai_tro_id' => $vaiTro->id,
                        'loai_giang_vien' => 'Giảng Viên Hướng Dẫn'
                    ]);
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Phân công giảng viên phản biện thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
