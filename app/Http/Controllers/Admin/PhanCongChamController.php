<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PhanCongCham;
use App\Models\DeTai;
use App\Models\TaiKhoan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PhanCongChamController extends Controller
{
    public function index()
    {
        $phanCongChams = PhanCongCham::with(['deTai', 'giangVienHuongDan', 'giangVienPhanBien', 'giangVienKhac'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.phan-cong-cham.index', compact('phanCongChams'));
    }

    public function create()
    {
        // Lấy danh sách đề tài đã được duyệt ở giai đoạn 1
        $deTais = DeTai::where('trang_thai', DeTai::TRANG_THAI_DANG_THUC_HIEN_GVHD)
            ->whereDoesntHave('phanCongCham')
            ->select('id', 'ma_de_tai', 'ten_de_tai', 'giang_vien_id', 'trang_thai')
            ->get();

        if ($deTais->isEmpty()) {
            return redirect()->route('admin.phan-cong-cham.index')
                ->with('error', 'Không có đề tài nào đủ điều kiện để phân công chấm. Đề tài phải ở trạng thái "Đang thực hiện (GVHD)" và chưa được phân công chấm.');
        }

        // Lấy danh sách giảng viên
        $giangViens = TaiKhoan::where('vai_tro', 'giang_vien')
            ->select('id', 'ten')
            ->get();

        return view('admin.phan-cong-cham.create', compact('deTais', 'giangViens'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'de_tai_id' => 'required|exists:de_tais,id',
                'giang_vien_phan_bien_id' => 'required|exists:tai_khoans,id',
                'giang_vien_khac_id' => 'required|exists:tai_khoans,id',
                'ngay_phan_cong' => 'required|date'
            ]);

            // Kiểm tra đề tài đã được phân công chấm chưa trong đợt báo cáo này
            if (PhanCongCham::where('de_tai_id', $request->de_tai_id)
                ->exists()) {
                return redirect()->back()->with('error', 'Đề tài này đã được phân công chấm');
            }

            // Kiểm tra đề tài có trạng thái phù hợp không
            $deTai = DeTai::findOrFail($request->de_tai_id);
            if ($deTai->trang_thai !== DeTai::TRANG_THAI_DANG_THUC_HIEN_GVHD) {
                return redirect()->back()->with('error', 'Đề tài này chưa được duyệt ở giai đoạn 1');
            }

            // Kiểm tra giảng viên hướng dẫn
            if ($deTai->giang_vien_id === $request->giang_vien_phan_bien_id ||
                $deTai->giang_vien_id === $request->giang_vien_khac_id) {
                return redirect()->back()->with('error', 'Giảng viên hướng dẫn không thể là giảng viên phản biện hoặc giảng viên khác');
            }

            // Kiểm tra các giảng viên không được trùng nhau
            if ($request->giang_vien_phan_bien_id === $request->giang_vien_khac_id) {
                return redirect()->back()->with('error', 'Các giảng viên không được trùng nhau');
            }

            DB::beginTransaction();
            try {
                // Tạo phân công chấm mới
                $phanCongCham = PhanCongCham::create([
                    'de_tai_id' => $request->de_tai_id,
                    'giang_vien_huong_dan_id' => $deTai->giang_vien_id,
                    'giang_vien_phan_bien_id' => $request->giang_vien_phan_bien_id,
                    'giang_vien_khac_id' => $request->giang_vien_khac_id,
                    'ngay_phan_cong' => $request->ngay_phan_cong
                ]);

                DB::commit();
                return redirect()->route('admin.phan-cong-cham.index')->with('success', 'Phân công chấm thành công');
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $phanCongCham = PhanCongCham::with(['deTai', 'giangVienHuongDan', 'giangVienPhanBien', 'giangVienKhac'])
            ->findOrFail($id);

        // Lấy danh sách đề tài đã được duyệt ở giai đoạn 1
        $deTais = DeTai::where('trang_thai', DeTai::TRANG_THAI_DANG_THUC_HIEN_GVHD)
            ->where(function($query) use ($phanCongCham) {
                $query->whereDoesntHave('phanCongCham')
                    ->orWhere('id', $phanCongCham->de_tai_id);
            })
            ->select('id', 'ma_de_tai', 'ten_de_tai', 'giang_vien_id', 'trang_thai')
            ->get();

        // Lấy danh sách giảng viên
        $giangViens = TaiKhoan::where('vai_tro', 'giang_vien')
            ->select('id', 'ten')
            ->get();

        return view('admin.phan-cong-cham.edit', compact('phanCongCham', 'giangViens', 'deTais'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'de_tai_id' => 'required|exists:de_tais,id',
            'giang_vien_huong_dan_id' => 'required|exists:tai_khoans,id',
            'giang_vien_phan_bien_id' => 'required|exists:tai_khoans,id',
            'giang_vien_khac_id' => 'required|exists:tai_khoans,id',
            'ngay_phan_cong' => 'required|date|after_or_equal:today',
        ], [
            'de_tai_id.required' => 'Vui lòng chọn đề tài',
            'de_tai_id.exists' => 'Đề tài không tồn tại',
            'giang_vien_huong_dan_id.required' => 'Vui lòng chọn giảng viên hướng dẫn',
            'giang_vien_huong_dan_id.exists' => 'Giảng viên hướng dẫn không tồn tại',
            'giang_vien_phan_bien_id.required' => 'Vui lòng chọn giảng viên phản biện',
            'giang_vien_phan_bien_id.exists' => 'Giảng viên phản biện không tồn tại',
            'giang_vien_khac_id.required' => 'Vui lòng chọn giảng viên khác',
            'giang_vien_khac_id.exists' => 'Giảng viên khác không tồn tại',
            'ngay_phan_cong.required' => 'Vui lòng chọn ngày phân công',
            'ngay_phan_cong.date' => 'Ngày phân công không hợp lệ',
            'ngay_phan_cong.after_or_equal' => 'Ngày phân công phải lớn hơn hoặc bằng ngày hiện tại',
        ]);

        $phanCongCham = PhanCongCham::findOrFail($id);
        $phanCongCham->update($validated);

        return redirect()->route('admin.phan-cong-cham.index')
            ->with('success', 'Cập nhật phân công chấm thành công');
    }

    public function destroy($id)
    {
        try {
            $phanCongCham = PhanCongCham::findOrFail($id);
            $phanCongCham->delete();
            return redirect()->route('admin.phan-cong-cham.index')
                ->with('success', 'Xóa phân công chấm thành công');
        } catch (\Exception $e) {
            return redirect()->route('admin.phan-cong-cham.index')
                ->with('error', 'Không tìm thấy phân công chấm cần xóa');
        }
    }

    private function getDeTaiOptions()
    {
        return DeTai::where('trang_thai', DeTai::TRANG_THAI_DANG_THUC_HIEN_GVHD)
            ->whereDoesntHave('phanCongCham')
            ->get()
            ->map(function ($deTai) {
                return [
                    'id' => $deTai->id,
                    'text' => $deTai->ma_de_tai . ' - ' . $deTai->ten_de_tai
                ];
            });
    }

    private function getGiangVienOptions()
    {
        return TaiKhoan::where('vai_tro', 'giang_vien')->get()
            ->map(function ($giangVien) {
                return [
                    'id' => $giangVien->id,
                    'text' => $giangVien->ten_tai_khoan
                ];
            });
    }
} 