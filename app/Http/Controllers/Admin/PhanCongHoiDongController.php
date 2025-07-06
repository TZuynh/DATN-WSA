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

        return view('admin.phan-cong-hoi-dong.index', compact('phanCongVaiTros', 'taiKhoansChuaPhanCong'));
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
}
