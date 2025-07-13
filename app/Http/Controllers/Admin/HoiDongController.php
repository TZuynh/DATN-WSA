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
use Illuminate\Support\Facades\Log;

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
            $hoiDongMoi = \App\Models\HoiDong::findOrFail($request->hoi_dong_id);

            // Cập nhật chi tiết đề tài báo cáo
            $chiTiet = \App\Models\ChiTietDeTaiBaoCao::where('de_tai_id', $request->de_tai_id)->first();
            if (!$chiTiet) {
                throw new \Exception('Không tìm thấy chi tiết đề tài!');
            }

            $hoiDongCuId = $chiTiet->hoi_dong_id;
            
            // Kiểm tra không được chuyển về chính hội đồng hiện tại
            if ($hoiDongCuId == $request->hoi_dong_id) {
                throw new \Exception('Không thể chuyển đề tài về chính hội đồng hiện tại!');
            }

            $chiTiet->hoi_dong_id = $request->hoi_dong_id;
            $chiTiet->save();

            // Lấy giảng viên liên quan đến đề tài này (hướng dẫn và phản biện)
            $giangVienDeTai = [];
            
            // Lấy giảng viên hướng dẫn của đề tài
            if ($deTai->giang_vien_id) {
                $giangVienDeTai[] = $deTai->giang_vien_id;
                Log::info("Thêm giảng viên hướng dẫn: {$deTai->giang_vien_id}");
            }
            
            // Lấy giảng viên phản biện từ phân công chấm
            $phanCongChamDeTai = \App\Models\PhanCongCham::where('de_tai_id', $request->de_tai_id)->first();
            if ($phanCongChamDeTai && $phanCongChamDeTai->giang_vien_id) {
                $giangVienDeTai[] = $phanCongChamDeTai->giang_vien_id;
                Log::info("Thêm giảng viên phản biện từ phân công chấm: {$phanCongChamDeTai->giang_vien_id}");
            }
            
            // Lấy thêm giảng viên phản biện từ phân công vai trò có loại giảng viên phản biện
            $giangVienPhanBienIds = \App\Models\PhanCongVaiTro::where('hoi_dong_id', $hoiDongCuId)
                ->where('loai_giang_vien', 'Giảng Viên Phản Biện')
                ->pluck('tai_khoan_id')
                ->toArray();
            
            if (!empty($giangVienPhanBienIds)) {
                Log::info("Thêm giảng viên phản biện từ phân công vai trò: " . implode(', ', $giangVienPhanBienIds));
            }
            
            $giangVienDeTai = array_merge($giangVienDeTai, $giangVienPhanBienIds);
            $giangVienDeTai = array_unique($giangVienDeTai); // Loại bỏ trùng lặp
            
            Log::info("Danh sách giảng viên liên quan đến đề tài: " . implode(', ', $giangVienDeTai));
            
            // Kiểm tra vai trò của giảng viên trong hội đồng hiện tại (hội đồng mà đề tài đang thuộc về)
            $giangVienKhongDuocChuyen = [];
            
            foreach ($giangVienDeTai as $giangVienId) {
                // Kiểm tra vai trò hiện tại của giảng viên trong hội đồng mà đề tài đang thuộc về
                $phanCongHienTai = \App\Models\PhanCongVaiTro::where('hoi_dong_id', $hoiDongCuId)
                    ->where('tai_khoan_id', $giangVienId)
                    ->whereNull('de_tai_id')
                    ->with(['taiKhoan', 'vaiTro'])
                    ->first();
                
                if ($phanCongHienTai && $phanCongHienTai->vaiTro) {
                    $vaiTroTen = $phanCongHienTai->vaiTro->ten;
                    $giangVienTen = $phanCongHienTai->taiKhoan->ten ?? 'N/A';
                    
                    // Debug: Log thông tin để kiểm tra
                    Log::info("Kiểm tra giảng viên: {$giangVienTen}, Vai trò: {$vaiTroTen}");
                    
                    if (str_contains($vaiTroTen, 'Trưởng tiểu ban') || str_contains($vaiTroTen, 'Thư ký')) {
                        $giangVienKhongDuocChuyen[] = [
                            'ten' => $giangVienTen,
                            'vai_tro' => $vaiTroTen,
                            'phan_cong' => $phanCongHienTai
                        ];
                        Log::info("Giảng viên {$giangVienTen} bị chặn chuyển vì vai trò: {$vaiTroTen}");
                    } else {
                        Log::info("Giảng viên {$giangVienTen} có thể chuyển với vai trò: {$vaiTroTen}");
                    }
                } else {
                    // Debug: Log trường hợp không tìm thấy phân công
                    $giangVien = \App\Models\TaiKhoan::find($giangVienId);
                    $giangVienTen = $giangVien ? $giangVien->ten : 'N/A';
                    Log::info("Không tìm thấy phân công cho giảng viên: {$giangVienTen} trong hội đồng {$hoiDongCuId} - có thể giảng viên này không thuộc hội đồng hiện tại");
                }
            }
            
            // Nếu có giảng viên không được chuyển, chặn hoàn toàn việc chuyển đề tài
            if (!empty($giangVienKhongDuocChuyen)) {
                $danhSachGiangVien = implode(', ', array_map(function($gv) {
                    return $gv['ten'] . ' (' . $gv['vai_tro'] . ')';
                }, $giangVienKhongDuocChuyen));
                
                Log::warning("Chặn chuyển đề tài vì có giảng viên với vai trò quan trọng: {$danhSachGiangVien}");
                
                throw new \Exception("Không thể chuyển đề tài vì có giảng viên với vai trò quan trọng: {$danhSachGiangVien}. Vui lòng thay đổi vai trò của họ thành 'Thành viên' trước khi chuyển đề tài.");
            }
            
            // Debug: Log thông tin tổng quan
            Log::info("Danh sách giảng viên liên quan đến đề tài: " . implode(', ', $giangVienDeTai));
            Log::info("Số giảng viên bị chặn chuyển: " . count($giangVienKhongDuocChuyen));
            
            // Chuyển tất cả giảng viên liên quan đến đề tài
            foreach ($giangVienDeTai as $giangVienId) {
                // Tìm phân công hiện tại của giảng viên trong hội đồng cũ
                $phanCongHienTai = \App\Models\PhanCongVaiTro::where('hoi_dong_id', $hoiDongCuId)
                    ->where('tai_khoan_id', $giangVienId)
                    ->whereNull('de_tai_id')
                    ->first();
                
                if ($phanCongHienTai) {
                    Log::info("Tìm thấy phân công cho giảng viên {$giangVienId} trong hội đồng cũ, bắt đầu chuyển...");
                    
                    // Kiểm tra giảng viên đã có trong hội đồng mới chưa
                    $phanCongDaTonTai = \App\Models\PhanCongVaiTro::where('hoi_dong_id', $request->hoi_dong_id)
                        ->where('tai_khoan_id', $giangVienId)
                        ->whereNull('de_tai_id')
                        ->first();

                    if (!$phanCongDaTonTai) {
                        // Tạo phân công mới cho giảng viên
                        \App\Models\PhanCongVaiTro::create([
                            'hoi_dong_id' => $request->hoi_dong_id,
                            'tai_khoan_id' => $giangVienId,
                            'vai_tro_id' => $phanCongHienTai->vai_tro_id,
                            'loai_giang_vien' => $phanCongHienTai->loai_giang_vien
                        ]);
                        Log::info("Đã tạo phân công mới cho giảng viên {$giangVienId} trong hội đồng mới");
                    } else {
                        // Cập nhật vai trò và loại giảng viên nếu đã có phân công
                        $phanCongDaTonTai->update([
                            'vai_tro_id' => $phanCongHienTai->vai_tro_id,
                            'loai_giang_vien' => $phanCongHienTai->loai_giang_vien
                        ]);
                        Log::info("Đã cập nhật phân công cho giảng viên {$giangVienId} trong hội đồng mới");
                    }
                    
                    // Xóa phân công của giảng viên khỏi hội đồng hiện tại
                    $phanCongHienTai->delete();
                    Log::info("Đã xóa phân công của giảng viên {$giangVienId} khỏi hội đồng cũ");
                    
                    $giangVien = \App\Models\TaiKhoan::find($giangVienId);
                    $giangVienTen = $giangVien ? $giangVien->ten : 'N/A';
                    Log::info("Đã chuyển giảng viên: {$giangVienTen} sang hội đồng mới");
                } else {
                    Log::info("Không tìm thấy phân công cho giảng viên {$giangVienId} trong hội đồng cũ - có thể giảng viên này không thuộc hội đồng hiện tại");
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
            
            // Lấy danh sách tên giảng viên đã chuyển
            $giangVienDaChuyen = [];
            foreach ($giangVienDeTai as $giangVienId) {
                $giangVien = \App\Models\TaiKhoan::find($giangVienId);
                if ($giangVien) {
                    $giangVienDaChuyen[] = $giangVien->ten;
                }
            }
            
            $message = 'Chuyển hội đồng thành công! ';
            if (!empty($giangVienDaChuyen)) {
                $danhSachChuyen = implode(', ', $giangVienDaChuyen);
                $message .= "Đã chuyển đề tài và các giảng viên liên quan: {$danhSachChuyen}.";
            } else {
                $message .= "Đã chuyển đề tài thành công.";
            }
            
            return response()->json(['success' => true, 'message' => $message]);

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

    /**
     * Debug: Kiểm tra vai trò giảng viên trong hội đồng
     */
    public function debugVaiTroGiangVien(Request $request)
    {
        $hoiDongId = $request->hoi_dong_id;
        $giangVienId = $request->giang_vien_id;
        
        $phanCong = \App\Models\PhanCongVaiTro::where('hoi_dong_id', $hoiDongId)
            ->where('tai_khoan_id', $giangVienId)
            ->whereNull('de_tai_id')
            ->with(['taiKhoan', 'vaiTro'])
            ->first();
        
        if ($phanCong) {
            return response()->json([
                'success' => true,
                'data' => [
                    'giang_vien_ten' => $phanCong->taiKhoan->ten ?? 'N/A',
                    'vai_tro_ten' => $phanCong->vaiTro->ten ?? 'N/A',
                    'loai_giang_vien' => $phanCong->loai_giang_vien ?? 'N/A',
                    'co_the_chuyen' => !(str_contains($phanCong->vaiTro->ten ?? '', 'Trưởng tiểu ban') || str_contains($phanCong->vaiTro->ten ?? '', 'Thư ký'))
                ]
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy phân công cho giảng viên này'
            ]);
        }
    }

    /**
     * Lấy dữ liệu thành viên hội đồng để cập nhật AJAX
     */
    public function getThanhVienHoiDong(HoiDong $hoiDong)
    {
        $thanhVien = $hoiDong->phanCongVaiTros()
            ->whereNull('de_tai_id')
            ->with(['taiKhoan', 'vaiTro'])
            ->get()
            ->sortBy(function($phanCong) {
                // Sắp xếp theo thứ tự ưu tiên: Trưởng tiểu ban, Thư ký, Thành viên, sau đó theo tên
                $vaiTro = $phanCong->vaiTro->ten ?? '';
                $ten = $phanCong->taiKhoan->ten ?? '';
                
                $priority = 0;
                if (str_contains($vaiTro, 'Trưởng tiểu ban')) $priority = 1;
                elseif (str_contains($vaiTro, 'Thư ký')) $priority = 2;
                elseif (str_contains($vaiTro, 'Thành viên')) $priority = 3;
                else $priority = 4;
                
                return $priority . $ten;
            });

        if ($thanhVien->count() == 0) {
            return response()->json([
                'success' => true,
                'html' => '<div class="text-muted"><small>Chưa có thành viên nào trong hội đồng</small></div>'
            ]);
        }

        $html = '<div class="d-flex flex-column gap-2">';
        
        foreach ($thanhVien as $phanCong) {
            $html .= '<div class="d-flex align-items-center justify-content-between p-2 border rounded bg-light">';
            
            // Phần thông tin giảng viên
            $html .= '<div class="d-flex align-items-center">';
            $html .= '<i class="fas fa-user-tie text-primary me-2"></i>';
            $html .= '<strong>' . ($phanCong->taiKhoan->ten ?? 'N/A') . '</strong>';
            
            // Vai trò
            $vaiTroClass = 'text-muted';
            if (str_contains($phanCong->vaiTro->ten ?? '', 'Trưởng tiểu ban')) {
                $vaiTroClass = 'text-danger fw-bold';
            } elseif (str_contains($phanCong->vaiTro->ten ?? '', 'Thư ký')) {
                $vaiTroClass = 'text-primary fw-bold';
            }
            
            $html .= '<span class="ms-2 ' . $vaiTroClass . '">(' . ($phanCong->vaiTro->ten ?? 'N/A') . ')</span>';
            
            // Loại giảng viên
            if ($phanCong->loai_giang_vien == 'Giảng Viên Phản Biện') {
                $html .= '<span class="badge bg-warning ms-2"><i class="fas fa-user-check me-1"></i>Phản biện</span>';
            } elseif ($phanCong->loai_giang_vien == 'Giảng Viên Hướng Dẫn') {
                $html .= '<span class="badge bg-success ms-2"><i class="fas fa-user-graduate me-1"></i>Hướng dẫn</span>';
            }
            
            $html .= '</div>';
            
            // Phần nút thao tác
            $html .= '<div class="d-flex align-items-center">';
            if ($phanCong->taiKhoan && $phanCong->taiKhoan->deTais && $phanCong->taiKhoan->deTais->count() > 0) {
                $html .= '<button type="button" class="btn btn-sm btn-outline-info me-1" data-bs-toggle="modal" data-bs-target="#modalTatCaDeTai' . $phanCong->taiKhoan->id . '" title="Xem đề tài">';
                $html .= '<i class="fas fa-book"></i> ' . $phanCong->taiKhoan->deTais->count();
                $html .= '</button>';
            }
            $html .= '</div>';
            
            $html .= '</div>';
        }
        
        $html .= '</div>';

        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }
} 