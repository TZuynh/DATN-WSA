<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PhanCongCham;
use App\Models\DeTai;
use App\Models\ChiTietDeTaiBaoCao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\TaiKhoan;

class PhanCongChamController extends Controller
{
    public function index()
    {
        $phanCongChams = PhanCongCham::with([
            'deTai',
            'hoiDong',
            'hoiDong.phanCongVaiTros.taiKhoan',
        ])->latest()->paginate(10);

        return view('admin.phan-cong-cham.index', compact('phanCongChams'));
    }

    public function create()
    {
        $deTais = DeTai::where('trang_thai', DeTai::TRANG_THAI_DANG_THUC_HIEN_GVHD)
            ->whereDoesntHave('phanCongCham')
            ->whereHas('chiTietBaoCao.hoiDong') // Chỉ lấy đề tài đã được gán vào hội đồng
            ->select('id', 'ma_de_tai', 'ten_de_tai')
            ->get();

        $giangViens = TaiKhoan::where('vai_tro', 'giang_vien')->select('id', 'ten')->get();

        if ($deTais->isEmpty()) {
            return redirect()->route('admin.phan-cong-cham.index')
                ->with('error', 'Không có đề tài nào đủ điều kiện để phân công chấm. Đề tài phải ở trạng thái "Đang thực hiện", chưa được phân công chấm và đã được gán vào một hội đồng.');
        }

        return view('admin.phan-cong-cham.create', compact('deTais', 'giangViens'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'de_tai_id' => 'required|exists:de_tais,id|unique:phan_cong_chams,de_tai_id',
            'lich_cham' => 'required|date_format:Y-m-d H:i'
        ], [
            'de_tai_id.required' => 'Vui lòng chọn đề tài.',
            'de_tai_id.unique' => 'Đề tài này đã được phân công chấm.',
            'lich_cham.required' => 'Vui lòng chọn lịch chấm.',
            'lich_cham.date_format' => 'Định dạng lịch chấm không hợp lệ (YYYY-MM-DD HH:mm).',
        ]);

        try {
            DB::beginTransaction();

            $chiTiet = ChiTietDeTaiBaoCao::where('de_tai_id', $validated['de_tai_id'])->first();
            if (!$chiTiet || !$chiTiet->hoi_dong_id) {
                return redirect()->back()->with('error', 'Không tìm thấy hội đồng cho đề tài này. Vui lòng kiểm tra lại phân công hội đồng.');
            }

            PhanCongCham::create([
                'de_tai_id' => $validated['de_tai_id'],
                'hoi_dong_id' => $chiTiet->hoi_dong_id,
                'lich_cham' => $validated['lich_cham'],
            ]);

            DB::commit();
            return redirect()->route('admin.phan-cong-cham.index')->with('success', 'Phân công chấm thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $phanCongCham = PhanCongCham::with(['deTai', 'hoiDong'])->findOrFail($id);

        $deTais = DeTai::where('trang_thai', DeTai::TRANG_THAI_DANG_THUC_HIEN_GVHD)
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
}
