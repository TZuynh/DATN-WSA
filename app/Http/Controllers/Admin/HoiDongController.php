<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HoiDong;
use App\Models\DotBaoCao;
use App\Models\PhanCongVaiTro;
use App\Models\ChiTietDeTaiBaoCao;
use App\Models\LichCham;
use App\Models\BienBanNhanXet;
use App\Models\DeTai;
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
        $hoiDongs = HoiDong::with('dotBaoCao')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('admin.hoi-dong.index', compact('hoiDongs'));
    }

    /**
     * Hiển thị form tạo hội đồng mới
     */
    public function create()
    {
        $dotBaoCaos = DotBaoCao::all();
        return view('admin.hoi-dong.create', compact('dotBaoCaos'));
    }

    /**
     * Lưu hội đồng mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'ten' => 'required',
            'dot_bao_cao_id' => 'required|exists:dot_bao_caos,id'
        ]);

        try {
            DB::beginTransaction();

            // Tạo mã hội đồng tự động
            $maHoiDong = HoiDong::taoMaHoiDong($request->dot_bao_cao_id);
            
            // Tạo hội đồng mới với mã tự động
            HoiDong::create([
                'ma_hoi_dong' => $maHoiDong,
                'ten' => $request->ten,
                'dot_bao_cao_id' => $request->dot_bao_cao_id
            ]);

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
        $dotBaoCaos = DotBaoCao::all();
        return view('admin.hoi-dong.edit', compact('hoiDong', 'dotBaoCaos'));
    }

    /**
     * Cập nhật hội đồng
     */
    public function update(Request $request, HoiDong $hoiDong)
    {
        $request->validate([
            'ten' => 'required',
            'dot_bao_cao_id' => 'required|exists:dot_bao_caos,id'
        ]);

        try {
            DB::beginTransaction();

            // Cập nhật thông tin hội đồng
            $hoiDong->update([
                'ten' => $request->ten,
                'dot_bao_cao_id' => $request->dot_bao_cao_id
            ]);

            DB::commit();
            return redirect()->route('admin.hoi-dong.index')
                ->with('success', 'Cập nhật hội đồng thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
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
            'chiTietBaoCaos' => function($query) {
                $query->with([
                    'deTai' => function($query) {
                        $query->with(['giangVien', 'nhom']);
                    }
                ]);
            },
            'phanCongVaiTros' => function($query) {
                $query->with([
                    'taiKhoan' => function($query) {
                        $query->with(['deTais' => function($query) {
                            $query->where('trang_thai', '!=', DeTai::TRANG_THAI_KHONG_XAY_RA_GVHD)
                                ->where('trang_thai', '!=', DeTai::TRANG_THAI_KHONG_XAY_RA_GVPB);
                        }]);
                    },
                    'vaiTro'
                ]);
            }
        ]);

        // Lấy danh sách đề tài chưa thuộc hội đồng nào
        $deTais = DeTai::whereDoesntHave('chiTietBaoCaos', function($query) use ($hoiDong) {
            $query->where('hoi_dong_id', '!=', $hoiDong->id);
        })->get();

        return view('admin.hoi-dong.chi-tiet', compact('hoiDong', 'deTais'));
    }

    /**
     * Thêm đề tài vào hội đồng
     */
    public function themDeTai(Request $request, HoiDong $hoiDong)
    {
        $request->validate([
            'de_tai_id' => 'required|exists:de_tais,id'
        ]);

        try {
            DB::beginTransaction();

            // Kiểm tra xem đề tài đã thuộc hội đồng nào chưa
            $existingChiTiet = ChiTietDeTaiBaoCao::where('de_tai_id', $request->de_tai_id)
                ->where('hoi_dong_id', '!=', $hoiDong->id)
                ->first();

            if ($existingChiTiet) {
                throw new \Exception('Đề tài này đã thuộc một hội đồng khác.');
            }

            // Tạo chi tiết báo cáo mới
            ChiTietDeTaiBaoCao::create([
                'hoi_dong_id' => $hoiDong->id,
                'de_tai_id' => $request->de_tai_id,
                'trang_thai' => 'dang_thuc_hien'
            ]);

            DB::commit();
            return redirect()->route('admin.hoi-dong.show', $hoiDong->id)
                ->with('success', 'Thêm đề tài vào hội đồng thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.hoi-dong.show', $hoiDong->id)
                ->with('error', 'Không thể thêm đề tài: ' . $e->getMessage());
        }
    }

    /**
     * Xóa đề tài khỏi hội đồng
     */
    public function xoaDeTai(HoiDong $hoiDong, ChiTietDeTaiBaoCao $chiTietBaoCao)
    {
        try {
            DB::beginTransaction();

            // Kiểm tra xem chi tiết báo cáo có thuộc hội đồng này không
            if ($chiTietBaoCao->hoi_dong_id !== $hoiDong->id) {
                throw new \Exception('Chi tiết báo cáo không thuộc hội đồng này.');
            }

            $chiTietBaoCao->delete();

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
} 