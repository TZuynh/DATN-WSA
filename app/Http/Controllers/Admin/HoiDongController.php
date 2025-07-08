<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HoiDong;
use App\Models\DotBaoCao;
use App\Models\PhanCongVaiTro;
use App\Models\ChiTietDeTaiBaoCao;
use App\Models\LichCham;
use App\Models\BienBanNhanXet;
use App\Models\Phong;
use App\Models\DeTai;
use App\Models\TaiKhoan;
use App\Models\Nhom;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class HoiDongController extends Controller
{
    /**
     * Hiển thị danh sách hội đồng
     */
    public function index()
    {
        $hoiDongs = HoiDong::with(['dotBaoCao', 'phong'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        // Lấy dữ liệu cho modal thêm đề tài
        $dotBaoCaos = DotBaoCao::with('hocKy')->get();
        $nhoms = Nhom::all();
        $giangViens = \App\Models\TaiKhoan::with('nhoms')->where('vai_tro', 'giang_vien')->get();
            
        return view('admin.hoi-dong.index', compact('hoiDongs', 'dotBaoCaos', 'nhoms', 'giangViens'));
    }

    /**
     * Hiển thị form tạo hội đồng mới
     */
    public function create()
    {
        $phongs = Phong::all();
        $dotBaoCaos = DotBaoCao::with('hocKy')->get();
        $nhoms = Nhom::all();
        return view('admin.hoi-dong.create', compact('phongs', 'dotBaoCaos', 'nhoms'));
    }

    /**
     * Lưu hội đồng mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'ten' => 'required|unique:hoi_dongs,ten',
            'dot_bao_cao_id' => 'required|exists:dot_bao_caos,id',
            'phong_id' => [
                'nullable',
                'exists:phongs,id',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value) {
                        $phongDaSuDung = HoiDong::where('dot_bao_cao_id', $request->dot_bao_cao_id)
                            ->where('phong_id', $value)
                            ->exists();
                        
                        if ($phongDaSuDung) {
                            $fail('Phòng này đã được sử dụng trong đợt báo cáo này.');
                        }
                    }
                },
            ],
            'thoi_gian_bat_dau' => 'required|date',
            'ten_de_tai' => 'nullable|string|max:255',
            'dot_bao_cao_de_tai' => 'nullable|exists:dot_bao_caos,id',
            'nhom_id' => 'nullable|exists:nhoms,id',
            'giang_vien_id' => 'nullable|exists:tai_khoans,id'
        ], [
            'ten.unique' => 'Tên hội đồng đã tồn tại.',
            'ten.required' => 'Vui lòng nhập tên hội đồng.',
        ]);

        try {
            DB::beginTransaction();

            // Tạo mã hội đồng tự động
            $maHoiDong = HoiDong::taoMaHoiDong($request->dot_bao_cao_id);
            
            // Tạo hội đồng mới với mã tự động
            $hoiDong = HoiDong::create([
                'ma_hoi_dong' => $maHoiDong,
                'ten' => $request->ten,
                'dot_bao_cao_id' => $request->dot_bao_cao_id,
                'phong_id' => $request->phong_id,
                'thoi_gian_bat_dau' => $request->thoi_gian_bat_dau
            ]);

            // Tạo đề tài nếu có thông tin
            if ($request->filled('ten_de_tai') && $request->filled('dot_bao_cao_de_tai')) {
                $deTai = DeTai::create([
                    'ten_de_tai' => $request->ten_de_tai,
                    'dot_bao_cao_id' => $request->dot_bao_cao_de_tai,
                    'nhom_id' => $request->nhom_id ?: null,
                    'giang_vien_id' => $request->giang_vien_id ?: null,
                    'trang_thai' => DeTai::TRANG_THAI_CHO_DUYET
                ]);

                // Tạo chi tiết đề tài báo cáo
                ChiTietDeTaiBaoCao::create([
                    'hoi_dong_id' => $hoiDong->id,
                    'de_tai_id' => $deTai->id,
                    'dot_bao_cao_id' => $request->dot_bao_cao_de_tai,
                    'trang_thai' => 0 // Trạng thái mặc định là chờ duyệt
                ]);

                // Cập nhật nhóm nếu có
                if ($request->filled('nhom_id')) {
                    $nhom = Nhom::find($request->nhom_id);
                    if ($nhom) {
                        $nhom->update(['de_tai_id' => $deTai->id]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('admin.hoi-dong.index')
                ->with('success', 'Thêm hội đồng thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Hiển thị form sửa hội đồng
     */
    public function edit(HoiDong $hoiDong)
    {
        $dotBaoCaos = DotBaoCao::with('hocKy')->get();
        $phongs = Phong::all();
        return view('admin.hoi-dong.edit', compact('hoiDong', 'dotBaoCaos', 'phongs'));
    }

    /**
     * Cập nhật hội đồng
     */
    public function update(Request $request, HoiDong $hoiDong)
    {
        $request->validate([
            'ma_hoi_dong' => 'required|string|max:255',
            'ten' => 'required|string|max:255|unique:hoi_dongs,ten,' . $hoiDong->id,
            'dot_bao_cao_id' => 'required|exists:dot_bao_caos,id',
            'phong_id' => [
                'nullable',
                'exists:phongs,id',
                function ($attribute, $value, $fail) use ($request, $hoiDong) {
                    if ($value) {
                        $phongDaSuDung = HoiDong::where('dot_bao_cao_id', $request->dot_bao_cao_id)
                            ->where('phong_id', $value)
                            ->where('id', '!=', $hoiDong->id)
                            ->exists();
                        
                        if ($phongDaSuDung) {
                            $fail('Phòng này đã được sử dụng trong đợt báo cáo này.');
                        }
                    }
                },
            ],
            'thoi_gian_bat_dau' => 'required|date'
        ], [
            'ten.unique' => 'Tên hội đồng đã tồn tại.',
            'ten.required' => 'Vui lòng nhập tên hội đồng.',
        ]);

        try {
            DB::beginTransaction();

            $hoiDong->update([
                'ma_hoi_dong' => $request->ma_hoi_dong,
                'ten' => $request->ten,
                'dot_bao_cao_id' => $request->dot_bao_cao_id,
                'phong_id' => $request->phong_id,
                'thoi_gian_bat_dau' => $request->thoi_gian_bat_dau
            ]);

            DB::commit();
            return redirect()->route('admin.hoi-dong.index')
                ->with('success', 'Cập nhật hội đồng thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi cập nhật: ' . $e->getMessage());
        }
    }

    /**
     * Xóa hội đồng
     */
    public function destroy(HoiDong $hoiDong)
    {
        try {
            DB::beginTransaction();

            // Xóa các phân công vai trò liên quan
            if (Schema::hasTable('phan_cong_vai_tros')) {
                PhanCongVaiTro::where('hoi_dong_id', $hoiDong->id)->delete();
            }

            // Xóa các chi tiết đề tài báo cáo liên quan
            if (Schema::hasTable('chi_tiet_de_tai_bao_caos')) {
                ChiTietDeTaiBaoCao::where('hoi_dong_id', $hoiDong->id)->delete();
            }

            // Xóa các lịch chấm liên quan
            if (Schema::hasTable('lich_chams')) {
                LichCham::where('hoi_dong_id', $hoiDong->id)->delete();
            }

            // Xóa hội đồng
            $hoiDong->delete();

            DB::commit();

            return redirect()->route('admin.hoi-dong.index')
                ->with('success', 'Xóa hội đồng thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.hoi-dong.index')
                ->with('error', 'Có lỗi xảy ra khi xóa hội đồng: ' . $e->getMessage());
        }
    }

    /**
     * Hiển thị chi tiết hội đồng
     */
    public function show(HoiDong $hoiDong)
    {
        // Load các quan hệ cần thiết
        $hoiDong->load([
            'dotBaoCao',
            'phong',
            'phanCongVaiTros' => function($query) {
                $query->with(['taiKhoan', 'vaiTro']);
            }
        ]);
        $giangViens = \App\Models\TaiKhoan::with('nhoms')->where('vai_tro', 'giang_vien')->get();

        return view('admin.hoi-dong.chi-tiet', compact('hoiDong', 'giangViens'));
    }

    /**
     * Thêm lịch chấm
     */
    public function themLichCham(Request $request, HoiDong $hoiDong)
    {
        $request->validate([
            'ngay_cham' => 'required|date',
            'thoi_gian_bat_dau' => 'required',
            'thoi_gian_ket_thuc' => 'required',
            'dia_diem' => 'required|string|max:255'
        ]);

        try {
            DB::beginTransaction();

            // Kiểm tra xung đột lịch chấm
            $ngayCham = Carbon::parse($request->ngay_cham);
            $thoiGianBatDau = Carbon::parse($request->thoi_gian_bat_dau);
            $thoiGianKetThuc = Carbon::parse($request->thoi_gian_ket_thuc);

            $xungDot = LichCham::where('hoi_dong_id', $hoiDong->id)
                ->where('ngay_cham', $ngayCham)
                ->where(function ($query) use ($thoiGianBatDau, $thoiGianKetThuc) {
                    $query->whereBetween('thoi_gian_bat_dau', [$thoiGianBatDau, $thoiGianKetThuc])
                        ->orWhereBetween('thoi_gian_ket_thuc', [$thoiGianBatDau, $thoiGianKetThuc]);
                })
                ->exists();

            if ($xungDot) {
                throw new \Exception('Thời gian này đã có lịch chấm khác.');
            }

            // Tạo lịch chấm mới
            LichCham::create([
                'hoi_dong_id' => $hoiDong->id,
                'ngay_cham' => $ngayCham,
                'thoi_gian_bat_dau' => $thoiGianBatDau,
                'thoi_gian_ket_thuc' => $thoiGianKetThuc,
                'dia_diem' => $request->dia_diem,
                'trang_thai' => 'chua_dien_ra'
            ]);

            DB::commit();
            return redirect()->route('admin.hoi-dong.show', $hoiDong->id)
                ->with('success', 'Thêm lịch chấm thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.hoi-dong.show', $hoiDong->id)
                ->with('error', 'Không thể thêm lịch chấm: ' . $e->getMessage());
        }
    }

    /**
     * Xóa lịch chấm
     */
    public function xoaLichCham(HoiDong $hoiDong, LichCham $lichCham)
    {
        try {
            DB::beginTransaction();

            // Kiểm tra xem lịch chấm có thuộc hội đồng này không
            if ($lichCham->hoi_dong_id !== $hoiDong->id) {
                throw new \Exception('Lịch chấm không thuộc hội đồng này.');
            }

            // Kiểm tra xem lịch chấm đã diễn ra chưa
            if ($lichCham->trang_thai === 'da_ket_thuc') {
                throw new \Exception('Không thể xóa lịch chấm đã kết thúc.');
            }

            $lichCham->delete();

            DB::commit();
            return redirect()->route('admin.hoi-dong.show', $hoiDong->id)
                ->with('success', 'Xóa lịch chấm thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.hoi-dong.show', $hoiDong->id)
                ->with('error', 'Không thể xóa lịch chấm: ' . $e->getMessage());
        }
    }

    /**
     * Xóa đề tài khỏi hội đồng
     */
    public function xoaDeTai(HoiDong $hoiDong, DeTai $deTai)
    {
        try {
            DB::beginTransaction();

            // Kiểm tra xem đề tài có thuộc hội đồng này không
            $chiTiet = ChiTietDeTaiBaoCao::where('hoi_dong_id', $hoiDong->id)
                ->where('de_tai_id', $deTai->id)
                ->first();

            if (!$chiTiet) {
                throw new \Exception('Đề tài không thuộc hội đồng này.');
            }

            // Xóa chi tiết đề tài báo cáo
            $chiTiet->delete();

            // Xóa đề tài
            $deTai->delete();

            DB::commit();
            return redirect()->route('admin.hoi-dong.show', $hoiDong->id)
                ->with('success', 'Xóa đề tài khỏi hội đồng thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.hoi-dong.show', $hoiDong->id)
                ->with('error', 'Không thể xóa đề tài: ' . $e->getMessage());
        }
    }

    /**
     * Chuyển đề tài sang hội đồng khác
     */
    public function chuyenDeTaiSangHoiDong(Request $request)
    {
        $request->validate([
            'de_tai_id' => 'required|exists:de_tais,id',
            'hoi_dong_id' => 'required|exists:hoi_dongs,id',
        ]);

        try {
            DB::beginTransaction();

            $deTai = \App\Models\DeTai::findOrFail($request->de_tai_id);
            $hoiDongCu = \App\Models\HoiDong::find($request->hoi_dong_id);
            $hoiDongMoi = \App\Models\HoiDong::findOrFail($request->hoi_dong_id);

            // Cập nhật chi tiết đề tài báo cáo
            $chiTiet = \App\Models\ChiTietDeTaiBaoCao::where('de_tai_id', $request->de_tai_id)->first();
            if (!$chiTiet) {
                throw new \Exception('Không tìm thấy chi tiết đề tài!');
            }

            $hoiDongCuId = $chiTiet->hoi_dong_id;
            $chiTiet->hoi_dong_id = $request->hoi_dong_id;
            $chiTiet->save();

            // Lấy danh sách giảng viên phản biện và hướng dẫn từ hội đồng cũ
            $giangVienPhanBienIds = \App\Models\PhanCongVaiTro::where('hoi_dong_id', $hoiDongCuId)
                ->where('loai_giang_vien', 'Giảng Viên Phản Biện')
                ->pluck('tai_khoan_id')
                ->toArray();

            $giangVienHuongDanIds = \App\Models\PhanCongVaiTro::where('hoi_dong_id', $hoiDongCuId)
                ->where('loai_giang_vien', 'Giảng Viên Hướng Dẫn')
                ->pluck('tai_khoan_id')
                ->toArray();

            // Thêm giảng viên phản biện vào hội đồng mới (nếu chưa có)
            foreach ($giangVienPhanBienIds as $giangVienId) {
                $phanCong = \App\Models\PhanCongVaiTro::where('hoi_dong_id', $request->hoi_dong_id)
                    ->where('tai_khoan_id', $giangVienId)
                    ->first();

                if (!$phanCong) {
                    // Tạo phân công mới cho giảng viên phản biện
                    $vaiTro = \App\Models\VaiTro::firstOrCreate(
                        ['ten' => 'Thành viên'],
                        ['mo_ta' => 'Thành viên hội đồng']
                    );
                    \App\Models\PhanCongVaiTro::create([
                        'hoi_dong_id' => $request->hoi_dong_id,
                        'tai_khoan_id' => $giangVienId,
                        'vai_tro_id' => $vaiTro->id,
                        'loai_giang_vien' => 'Giảng Viên Phản Biện'
                    ]);
                } else {
                    // Cập nhật loại giảng viên nếu đã có phân công
                    $phanCong->update(['loai_giang_vien' => 'Giảng Viên Phản Biện']);
                }
            }

            // Thêm giảng viên hướng dẫn vào hội đồng mới (nếu chưa có)
            foreach ($giangVienHuongDanIds as $giangVienId) {
                $phanCong = \App\Models\PhanCongVaiTro::where('hoi_dong_id', $request->hoi_dong_id)
                    ->where('tai_khoan_id', $giangVienId)
                    ->first();

                if (!$phanCong) {
                    // Tạo phân công mới cho giảng viên hướng dẫn
                    $vaiTro = \App\Models\VaiTro::firstOrCreate(
                        ['ten' => 'Thành viên'],
                        ['mo_ta' => 'Thành viên hội đồng']
                    );
                    \App\Models\PhanCongVaiTro::create([
                        'hoi_dong_id' => $request->hoi_dong_id,
                        'tai_khoan_id' => $giangVienId,
                        'vai_tro_id' => $vaiTro->id,
                        'loai_giang_vien' => 'Giảng Viên Hướng Dẫn'
                    ]);
                } else {
                    // Cập nhật loại giảng viên nếu đã có phân công
                    $phanCong->update(['loai_giang_vien' => 'Giảng Viên Hướng Dẫn']);
                }
            }

            // Cập nhật phân công chấm nếu có
            $phanCongCham = \App\Models\PhanCongCham::where('de_tai_id', $request->de_tai_id)->first();
            if ($phanCongCham) {
                $phanCongCham->update(['hoi_dong_id' => $request->hoi_dong_id]);
            }

            // Cập nhật lịch chấm nếu có
            $lichCham = \App\Models\LichCham::where('de_tai_id', $request->de_tai_id)->first();
            if ($lichCham) {
                $lichCham->update(['hoi_dong_id' => $request->hoi_dong_id]);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Chuyển hội đồng thành công! Giảng viên phản biện và hướng dẫn đã được giữ nguyên.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra: ' . $e->getMessage()], 500);
        }
    }

    public function themDeTai(Request $request, $hoiDongId)
    {
        $deTaiIds = $request->input('de_tai_ids', []);
        if (!is_array($deTaiIds)) {
            // Trường hợp cũ: chỉ 1 id
            $deTaiIds = [$request->input('de_tai_id')];
        }
        if (empty($deTaiIds) || !is_array($deTaiIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng chọn ít nhất một đề tài!'
            ]);
        }
        $hoiDong = \App\Models\HoiDong::find($hoiDongId);
        if (!$hoiDong) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy hội đồng!'
            ]);
        }
        $dotBaoCaoId = $hoiDong->dot_bao_cao_id;
        $added = 0;
        $skipped = 0;
        foreach ($deTaiIds as $deTaiId) {
            if (!\App\Models\DeTai::where('id', $deTaiId)->exists()) {
                $skipped++;
                continue;
            }
            $daTonTai = \App\Models\ChiTietDeTaiBaoCao::where('de_tai_id', $deTaiId)
                ->where('hoi_dong_id', $hoiDongId)
                ->exists();
            if ($daTonTai) {
                $skipped++;
                continue;
            }
            \App\Models\ChiTietDeTaiBaoCao::updateOrCreate(
                ['de_tai_id' => $deTaiId],
                [
                    'hoi_dong_id' => $hoiDongId,
                    'dot_bao_cao_id' => $dotBaoCaoId,
                ]
            );
            $added++;
        }
        $msg = "Đã thêm $added đề tài vào hội đồng.";
        if ($skipped > 0) {
            $msg .= " $skipped đề tài đã tồn tại hoặc không hợp lệ.";
        }
        return response()->json([
            'success' => true,
            'message' => $msg,
        ]);
    }
} 