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
                ->with('error', 'Không có đề tài nào đủ điều kiện để phản biện. Đề tài phải ở trạng thái "Đang thực hiện (GVHD)" và chưa được phản biện.');
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
                'lich_cham' => 'required|date_format:Y-m-d H:i'
            ], [
                'de_tai_id.required' => 'Vui lòng chọn đề tài',
                'de_tai_id.exists' => 'Đề tài không tồn tại',
                'giang_vien_phan_bien_id.required' => 'Vui lòng chọn giảng viên phản biện',
                'giang_vien_phan_bien_id.exists' => 'Giảng viên phản biện không tồn tại',
                'giang_vien_khac_id.required' => 'Vui lòng chọn giảng viên khác',
                'giang_vien_khac_id.exists' => 'Giảng viên khác không tồn tại',
                'lich_cham.required' => 'Vui lòng chọn lịch chấm',
                'lich_cham.date_format' => 'Định dạng lịch chấm không hợp lệ (YYYY-MM-DD HH:mm)',
            ]);

            // Kiểm tra đề tài đã được Phản biện chưa trong đợt báo cáo này
            if (PhanCongCham::where('de_tai_id', $request->de_tai_id)
                ->exists()) {
                return redirect()->back()->with('error', 'Đề tài này đã được phản biện');
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

            // Kiểm tra lịch chấm không được trong quá khứ
            if (strtotime($request->lich_cham) < time()) {
                return redirect()->back()->with('error', 'Lịch chấm không được trong quá khứ');
            }

            DB::beginTransaction();
            try {
                // Tạo Phản biện mới
                $phanCongCham = PhanCongCham::create([
                    'de_tai_id' => $request->de_tai_id,
                    'giang_vien_huong_dan_id' => $deTai->giang_vien_id,
                    'giang_vien_phan_bien_id' => $request->giang_vien_phan_bien_id,
                    'giang_vien_khac_id' => $request->giang_vien_khac_id,
                    'lich_cham' => $request->lich_cham
                ]);

                DB::commit();
                return redirect()->route('admin.phan-cong-cham.index')->with('success', 'Phản biện thành công');
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

        // Kiểm tra xem đề tài hiện tại có lịch chấm không
        $deTaiCoLichCham = \App\Models\LichCham::where('de_tai_id', $phanCongCham->de_tai_id)->exists();

        // Lấy danh sách đề tài đã được duyệt ở giai đoạn 1
        $deTais = DeTai::where('trang_thai', DeTai::TRANG_THAI_DANG_THUC_HIEN_GVHD)
            ->where(function($query) use ($phanCongCham) {
                $query->whereDoesntHave('phanCongCham')
                    ->orWhere('id', $phanCongCham->de_tai_id);
            })
            ->select('id', 'ma_de_tai', 'ten_de_tai', 'giang_vien_id', 'trang_thai')
            ->get();

        // Đảm bảo đề tài hiện tại luôn có trong danh sách
        if (!$deTais->contains('id', $phanCongCham->de_tai_id)) {
            $currentDeTai = DeTai::where('id', $phanCongCham->de_tai_id)
                ->select('id', 'ma_de_tai', 'ten_de_tai', 'giang_vien_id', 'trang_thai')
                ->first();
            if ($currentDeTai) {
                $deTais->push($currentDeTai);
            }
        }

        // Lấy danh sách giảng viên
        $giangViens = TaiKhoan::where('vai_tro', 'giang_vien')
            ->select('id', 'ten')
            ->get();

        return view('admin.phan-cong-cham.edit', compact('phanCongCham', 'giangViens', 'deTais', 'deTaiCoLichCham'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'de_tai_id' => 'required|exists:de_tais,id',
            'giang_vien_huong_dan_id' => 'required|exists:tai_khoans,id',
            'giang_vien_phan_bien_id' => 'required|exists:tai_khoans,id',
            'giang_vien_khac_id' => 'required|exists:tai_khoans,id',
            'lich_cham' => 'required|date_format:Y-m-d H:i',
        ], [
            'de_tai_id.required' => 'Vui lòng chọn đề tài',
            'de_tai_id.exists' => 'Đề tài không tồn tại',
            'giang_vien_huong_dan_id.required' => 'Vui lòng chọn giảng viên hướng dẫn',
            'giang_vien_huong_dan_id.exists' => 'Giảng viên hướng dẫn không tồn tại',
            'giang_vien_phan_bien_id.required' => 'Vui lòng chọn giảng viên phản biện',
            'giang_vien_phan_bien_id.exists' => 'Giảng viên phản biện không tồn tại',
            'giang_vien_khac_id.required' => 'Vui lòng chọn giảng viên khác',
            'giang_vien_khac_id.exists' => 'Giảng viên khác không tồn tại',
            'lich_cham.required' => 'Vui lòng chọn lịch chấm',
            'lich_cham.date_format' => 'Định dạng lịch chấm không hợp lệ (YYYY-MM-DD HH:mm)',
        ]);

        $phanCongCham = PhanCongCham::findOrFail($id);
        
        // Kiểm tra nếu thay đổi đề tài
        if ($phanCongCham->de_tai_id != $request->de_tai_id) {
            // Kiểm tra xem đề tài mới có lịch chấm chưa
            $deTaiMoi = DeTai::findOrFail($request->de_tai_id);
            $lichChamTonTai = \App\Models\LichCham::where('de_tai_id', $deTaiMoi->id)->exists();
            
            if ($lichChamTonTai) {
                return redirect()->back()->with('error', 'Đề tài này đã có lịch chấm. Không thể thay đổi phản biện.');
            }
            
            // Kiểm tra xem đề tài cũ có lịch chấm không
            $deTaiCu = DeTai::findOrFail($phanCongCham->de_tai_id);
            $lichChamCu = \App\Models\LichCham::where('de_tai_id', $deTaiCu->id)->exists();
            
            if ($lichChamCu) {
                return redirect()->back()->with('error', 'Đề tài hiện tại đã có lịch chấm. Không thể thay đổi phản biện.');
            }
        }
        
        $phanCongCham->update($validated);

        return redirect()->route('admin.phan-cong-cham.index')
            ->with('success', 'Cập nhật phản biện thành công');
    }

    public function destroy($id)
    {
        try {
            $phanCongCham = PhanCongCham::findOrFail($id);
            $phanCongCham->delete();
            return redirect()->route('admin.phan-cong-cham.index')
                ->with('success', 'Xóa phản biện thành công');
        } catch (\Exception $e) {
            return redirect()->route('admin.phan-cong-cham.index')
                ->with('error', 'Không tìm thấy phản biện cần xóa');
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