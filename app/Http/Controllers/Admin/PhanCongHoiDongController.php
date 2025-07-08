<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PhanCongVaiTro;
use App\Models\HoiDong;
use App\Models\TaiKhoan;
use App\Models\VaiTro;
use App\Models\DeTai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PhanCongHoiDongController extends Controller
{
    public function index()
    {
        $phanCongVaiTros = PhanCongVaiTro::with(['hoiDong', 'taiKhoan', 'vaiTro'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Lấy id các giảng viên đã được phân công vào bất kỳ hội đồng nào
        $giangVienDaPhanCongIds = PhanCongVaiTro::pluck('tai_khoan_id')->unique()->toArray();

        // Lấy danh sách giảng viên chưa có phân công
        $taiKhoansChuaPhanCong = TaiKhoan::where('vai_tro', 'giang_vien')
            ->whereNotIn('id', $giangVienDaPhanCongIds)
            ->get();

        // Lấy tất cả hội đồng, kèm đề tài và phân công vai trò
        $hoiDongs = HoiDong::with([
            'chiTietBaoCaos.deTai',
            'chiTietBaoCaos.deTai.giangVien',
            'phanCongVaiTros.taiKhoan',
            'phanCongVaiTros.vaiTro',
        ])->get();

        return view('admin.phan-cong-hoi-dong.index', compact('phanCongVaiTros', 'taiKhoansChuaPhanCong', 'hoiDongs'));
    }

    public function create(Request $request)
    {
        $hoiDongs = HoiDong::all();
        
        // Lấy danh sách giảng viên chưa được phân công vào bất kỳ hội đồng nào
        $giangVienDaPhanCongIds = PhanCongVaiTro::pluck('tai_khoan_id')->unique()->toArray();
        $taiKhoans = TaiKhoan::where('vai_tro', 'giang_vien')
            ->whereNotIn('id', $giangVienDaPhanCongIds)
            ->get();
            
        $vaiTros = VaiTro::all();

        $selectedHoiDong = $request->get('hoi_dong_id');
        $truongTieuBanId = VaiTro::where('ten', 'Trưởng tiểu ban')->value('id');
        $thuKyId = VaiTro::where('ten', 'Thư ký')->value('id');

        // Lấy danh sách vai trò đã được phân công trong hội đồng này
        $vaiTrosDaPhanCong = [];
        if ($selectedHoiDong) {
            $vaiTrosDaPhanCong = PhanCongVaiTro::where('hoi_dong_id', $selectedHoiDong)
                ->pluck('vai_tro_id')
                ->toArray();
        }

        // Lấy danh sách giảng viên đã được phân công trong hội đồng này
        $giangViensDaPhanCong = [];
        if ($selectedHoiDong) {
            $giangViensDaPhanCong = PhanCongVaiTro::where('hoi_dong_id', $selectedHoiDong)
                ->pluck('tai_khoan_id')
                ->toArray();
        }

        return view('admin.phan-cong-hoi-dong.create', compact(
            'hoiDongs',
            'taiKhoans',
            'vaiTros',
            'selectedHoiDong',
            'vaiTrosDaPhanCong',
            'giangViensDaPhanCong',
            'truongTieuBanId',
            'thuKyId'
        ));
    }

    public function store(Request $request)
    {
        $truongTieuBanId = VaiTro::where('ten', 'Trưởng tiểu ban')->value('id');
        $thuKyId = VaiTro::where('ten', 'Thư ký')->value('id');

        $request->validate([
            'hoi_dong_id' => 'required|exists:hoi_dongs,id',
            'tai_khoan_id' => 'required|exists:tai_khoans,id',
            'vai_tro_id' => 'required|exists:vai_tros,id'
        ]);

        try {
            DB::beginTransaction();

            // Kiểm tra tổng số giảng viên trong hội đồng
            $count = PhanCongVaiTro::where('hoi_dong_id', $request->hoi_dong_id)->count();
            if ($count >= 5) {
                throw new \Exception('Hội đồng này đã đủ 5 giảng viên.');
            }

            // Nếu là Trưởng tiểu ban hoặc Thư ký, chỉ được 1 người duy nhất trong hội đồng
            if (in_array($request->vai_tro_id, [$truongTieuBanId, $thuKyId])) {
                $existsRole = PhanCongVaiTro::where('hoi_dong_id', $request->hoi_dong_id)
                    ->where('vai_tro_id', $request->vai_tro_id)
                    ->exists();
                if ($existsRole) {
                    throw new \Exception('Vai trò này đã được phân công cho một giảng viên khác trong hội đồng!');
                }
            }

            // Kiểm tra giảng viên đã được phân công vào hội đồng khác chưa
            $existsGVInOtherHoiDong = PhanCongVaiTro::where('tai_khoan_id', $request->tai_khoan_id)
                ->where('hoi_dong_id', '!=', $request->hoi_dong_id)
                ->exists();
            if ($existsGVInOtherHoiDong) {
                throw new \Exception('Giảng viên này đã được phân công vào hội đồng khác! Mỗi giảng viên chỉ được phân công vào 1 hội đồng duy nhất.');
            }

            // Không cho trùng giảng viên trong cùng hội đồng
            $existsGV = PhanCongVaiTro::where('hoi_dong_id', $request->hoi_dong_id)
                ->where('tai_khoan_id', $request->tai_khoan_id)
                ->exists();
            if ($existsGV) {
                throw new \Exception('Giảng viên này đã được phân công vào hội đồng này!');
            }

            $data = $request->only(['hoi_dong_id', 'tai_khoan_id', 'vai_tro_id']);
            $phanCongVaiTro = PhanCongVaiTro::create($data);

            DB::commit();
            return redirect()->route('admin.phan-cong-hoi-dong.index')
                ->with('success', 'Phân công thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function edit(PhanCongVaiTro $phanCongVaiTro)
    {
        $hoiDongs = HoiDong::all();
        
        // Lấy danh sách giảng viên chưa được phân công vào bất kỳ hội đồng nào (trừ giảng viên đang sửa)
        $giangVienDaPhanCongIds = PhanCongVaiTro::where('id', '!=', $phanCongVaiTro->id)
            ->pluck('tai_khoan_id')
            ->unique()
            ->toArray();
        $taiKhoans = TaiKhoan::where('vai_tro', 'giang_vien')
            ->whereNotIn('id', $giangVienDaPhanCongIds)
            ->orWhere('id', $phanCongVaiTro->tai_khoan_id) // Bao gồm giảng viên đang sửa
            ->get();
            
        $truongTieuBanId = VaiTro::where('ten', 'Trưởng tiểu ban')->value('id');
        $thuKyId = VaiTro::where('ten', 'Thư ký')->value('id');

        // Lấy danh sách vai trò đã được phân công trong hội đồng này (trừ chính bản ghi đang sửa)
        $vaiTrosDaPhanCong = PhanCongVaiTro::where('hoi_dong_id', $phanCongVaiTro->hoi_dong_id)
            ->where('id', '!=', $phanCongVaiTro->id)
            ->pluck('vai_tro_id')
            ->toArray();

        // Lấy danh sách giảng viên đã được phân công trong hội đồng này (trừ chính bản ghi đang sửa)
        $giangViensDaPhanCong = PhanCongVaiTro::where('hoi_dong_id', $phanCongVaiTro->hoi_dong_id)
            ->where('id', '!=', $phanCongVaiTro->id)
            ->pluck('tai_khoan_id')
            ->toArray();

        $vaiTros = VaiTro::all();

        return view('admin.phan-cong-hoi-dong.edit', compact(
            'phanCongVaiTro',
            'hoiDongs',
            'taiKhoans',
            'vaiTros',
            'vaiTrosDaPhanCong',
            'giangViensDaPhanCong',
            'truongTieuBanId',
            'thuKyId'
        ));
    }

    public function update(Request $request, PhanCongVaiTro $phanCongVaiTro)
    {
        $truongTieuBanId = VaiTro::where('ten', 'Trưởng tiểu ban')->value('id');
        $thuKyId = VaiTro::where('ten', 'Thư ký')->value('id');

        $request->validate([
            'hoi_dong_id' => 'required|exists:hoi_dongs,id',
            'tai_khoan_id' => 'required|exists:tai_khoans,id',
            'vai_tro_id' => 'required|exists:vai_tros,id'
        ]);

        try {
            DB::beginTransaction();

            // Kiểm tra tổng số giảng viên trong hội đồng (trừ chính bản ghi đang sửa)
            $count = PhanCongVaiTro::where('hoi_dong_id', $request->hoi_dong_id)
                ->where('id', '!=', $phanCongVaiTro->id)
                ->count();
            if ($count >= 5) {
                throw new \Exception('Hội đồng này đã đủ 5 giảng viên.');
            }

            // Nếu là Trưởng tiểu ban hoặc Thư ký, chỉ được 1 người duy nhất trong hội đồng
            if (in_array($request->vai_tro_id, [$truongTieuBanId, $thuKyId])) {
                $existsRole = PhanCongVaiTro::where('hoi_dong_id', $request->hoi_dong_id)
                    ->where('vai_tro_id', $request->vai_tro_id)
                    ->where('id', '!=', $phanCongVaiTro->id)
                    ->exists();
                if ($existsRole) {
                    throw new \Exception('Vai trò này đã được phân công cho một giảng viên khác trong hội đồng!');
                }
            }

            // Kiểm tra giảng viên đã được phân công vào hội đồng khác chưa (trừ chính bản ghi đang sửa)
            $existsGVInOtherHoiDong = PhanCongVaiTro::where('tai_khoan_id', $request->tai_khoan_id)
                ->where('hoi_dong_id', '!=', $request->hoi_dong_id)
                ->where('id', '!=', $phanCongVaiTro->id)
                ->exists();
            if ($existsGVInOtherHoiDong) {
                throw new \Exception('Giảng viên này đã được phân công vào hội đồng khác! Mỗi giảng viên chỉ được phân công vào 1 hội đồng duy nhất.');
            }

            // Không cho trùng giảng viên trong cùng hội đồng (trừ chính bản ghi đang sửa)
            $existsGV = PhanCongVaiTro::where('hoi_dong_id', $request->hoi_dong_id)
                ->where('tai_khoan_id', $request->tai_khoan_id)
                ->where('id', '!=', $phanCongVaiTro->id)
                ->exists();
            if ($existsGV) {
                throw new \Exception('Giảng viên này đã được phân công vào hội đồng này!');
            }

            $data = $request->only(['hoi_dong_id', 'tai_khoan_id', 'vai_tro_id']);
            $phanCongVaiTro->update($data);

            DB::commit();
            return redirect()->route('admin.phan-cong-hoi-dong.index')
                ->with('success', 'Cập nhật phân công thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function destroy(PhanCongVaiTro $phanCongVaiTro)
    {
        $phanCongVaiTro->delete();

        return redirect()->route('admin.phan-cong-hoi-dong.index')
            ->with('success', 'Xóa phân công thành công.');
    }

    public function changeGiangVien(Request $request, $id)
    {
        $request->validate([
            'tai_khoan_id' => 'required|exists:tai_khoans,id'
        ]);
        $phanCong = PhanCongVaiTro::findOrFail($id);
        
        // Kiểm tra giảng viên mới đã được phân công vào hội đồng khác chưa
        $existsInOtherHoiDong = PhanCongVaiTro::where('tai_khoan_id', $request->tai_khoan_id)
            ->where('hoi_dong_id', '!=', $phanCong->hoi_dong_id)
            ->exists();
        if ($existsInOtherHoiDong) {
            return back()->withErrors(['error' => 'Giảng viên này đã được phân công vào hội đồng khác! Mỗi giảng viên chỉ được phân công vào 1 hội đồng duy nhất.']);
        }
        
        // Kiểm tra giảng viên mới chưa được phân công vào hội đồng này
        $exists = PhanCongVaiTro::where('hoi_dong_id', $phanCong->hoi_dong_id)
            ->where('tai_khoan_id', $request->tai_khoan_id)
            ->where('id', '!=', $phanCong->id)
            ->exists();
        if ($exists) {
            return back()->withErrors(['error' => 'Giảng viên này đã được phân công vào hội đồng này!']);
        }
        
        $phanCong->tai_khoan_id = $request->tai_khoan_id;
        $phanCong->save();
        return back()->with('success', 'Chuyển giảng viên thành công!');
    }

    public function swapGiangVien(Request $request)
    {
        $request->validate([
            'phan_cong_id_1' => 'required|exists:phan_cong_vai_tros,id',
            'phan_cong_id_2' => 'required|exists:phan_cong_vai_tros,id',
        ]);
        $pc1 = PhanCongVaiTro::findOrFail($request->phan_cong_id_1);
        $pc2 = PhanCongVaiTro::findOrFail($request->phan_cong_id_2);
        if ($pc1->vai_tro_id !== $pc2->vai_tro_id) {
            return back()->withErrors(['error' => 'Hai giảng viên phải cùng vai trò mới được hoán đổi!']);
        }
        $hd1 = $pc1->hoiDong;
        $hd2 = $pc2->hoiDong;
        if (!$hd1 || !$hd2) {
            return back()->withErrors(['error' => 'Không tìm thấy hội đồng!']);
        }
        $dot1 = $hd1->dotBaoCao;
        $dot2 = $hd2->dotBaoCao;
        if (!$dot1 || !$dot2) {
            return back()->withErrors(['error' => 'Không tìm thấy đợt báo cáo!']);
        }
        if (
            $dot1->nam_hoc != $dot2->nam_hoc ||
            $dot1->hoc_ky_id != $dot2->hoc_ky_id ||
            $hd1->thoi_gian_bat_dau != $hd2->thoi_gian_bat_dau
        ) {
            return back()->withErrors(['error' => 'Chỉ được hoán đổi giữa các hội đồng cùng ngày, cùng năm học, cùng học kỳ!']);
        }
        DB::beginTransaction();
        try {
            $hoiDong1 = $pc1->hoi_dong_id;
            $hoiDong2 = $pc2->hoi_dong_id;
            $pc1->hoi_dong_id = $hoiDong2;
            $pc2->hoi_dong_id = $hoiDong1;
            $pc1->save();
            $pc2->save();
            DB::commit();
            return back()->with('success', 'Hoán đổi giảng viên thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Có lỗi xảy ra khi hoán đổi: ' . $e->getMessage()]);
        }
    }

    public function addCham(Request $request)
    {
        $request->validate([
            'hoi_dong_id' => 'required|exists:hoi_dongs,id',
            'de_tai_id' => 'required|exists:de_tais,id',
            'tai_khoan_id' => 'required|exists:tai_khoans,id',
        ]);
        // Kiểm tra đã tồn tại phân công này chưa
        $exists = \App\Models\PhanCongVaiTro::where('hoi_dong_id', $request->hoi_dong_id)
            ->where('tai_khoan_id', $request->tai_khoan_id)
            ->where('de_tai_id', $request->de_tai_id)
            ->exists();
        if ($exists) {
            return back()->withErrors(['error' => 'Giảng viên này đã được phân công chấm đề tài này!']);
        }
        // Tạo mới phân công chấm
        \App\Models\PhanCongVaiTro::create([
            'hoi_dong_id' => $request->hoi_dong_id,
            'tai_khoan_id' => $request->tai_khoan_id,
            'de_tai_id' => $request->de_tai_id,
            'loai_giang_vien' => 'Giảng Viên Khác',
            'vai_tro_id' => null,
        ]);
        return back()->with('success', 'Thêm giảng viên chấm thành công!');
    }
}
