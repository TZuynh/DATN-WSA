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
        $taiKhoans = TaiKhoan::where('vai_tro', 'giang_vien')->get();
        $vaiTros = VaiTro::all();

        $phanBienId = VaiTro::where('ten', 'Giảng Viên Phản Biện')->value('id');
        $khacId = VaiTro::where('ten', 'Giảng Viên Khác')->value('id');

        $phanBienDaPhanCong = [];
        $khacDaPhanCong = [];

        $selectedHoiDong = $request->get('hoi_dong_id');
        $thanhVienId = VaiTro::where('ten', 'Thành viên')->value('id');
        $loaiGiangVienDaPhanCong = [];
        if ($selectedHoiDong) {
            $loaiGiangVienDaPhanCong = PhanCongVaiTro::where('hoi_dong_id', $selectedHoiDong)
                ->where('vai_tro_id', $thanhVienId)
                ->pluck('loai_giang_vien')
                ->toArray();
        }

        // Lấy danh sách giảng viên đang có đề tài
        $giangVienCoDeTai = DeTai::where('trang_thai', '!=', 4) // Không tính các đề tài đã bị từ chối
            ->pluck('giang_vien_id')
            ->unique()
            ->toArray();

        // Lấy danh sách vai trò không được phân công cho giảng viên có đề tài
        $vaiTroKhongDuocPhanCong = VaiTro::whereIn('ten', ['Trưởng tiểu ban', 'Thư ký'])
            ->pluck('id')
            ->toArray();

        $truongTieuBanId = VaiTro::where('ten', 'Trưởng tiểu ban')->value('id');
        $thuKyId = VaiTro::where('ten', 'Thư ký')->value('id');
        $thanhVienId = VaiTro::where('ten', 'Thành viên')->value('id');

        $giangViensDaPhanCong = [];
        if ($selectedHoiDong) {
            $giangViensDaPhanCong = PhanCongVaiTro::where('hoi_dong_id', $selectedHoiDong)
                ->where('id', '!=', $selectedHoiDong) // Loại trừ chính bản ghi đang sửa
                ->pluck('tai_khoan_id')
                ->toArray();
        }

        return view('admin.phan-cong-hoi-dong.create', compact(
            'hoiDongs', 
            'taiKhoans', 
            'vaiTros', 
            'selectedHoiDong',
            'giangVienCoDeTai',
            'vaiTroKhongDuocPhanCong',
            'phanBienDaPhanCong',
            'khacDaPhanCong',
            'phanBienId',
            'khacId',
            'loaiGiangVienDaPhanCong',
            'thanhVienId',
            'truongTieuBanId',
            'thuKyId',
            'giangViensDaPhanCong'
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

            // Kiểm tra trùng vai trò Giảng Viên Phản Biện hoặc Giảng Viên Khác trong cùng hội đồng
            $phanBienId = VaiTro::where('ten', 'Giảng Viên Phản Biện')->value('id');
            $khacId = VaiTro::where('ten', 'Giảng Viên Khác')->value('id');

            if (in_array($request->vai_tro_id, [$phanBienId, $khacId])) {
                $exists = PhanCongVaiTro::where('hoi_dong_id', $request->hoi_dong_id)
                    ->where('vai_tro_id', $request->vai_tro_id)
                    ->exists();
                if ($exists) {
                    throw new \Exception('Vai trò này đã được phân công cho một giảng viên khác trong hội đồng!');
                }
            }

            // Không cho giảng viên đã ở hội đồng khác được phân công tiếp
            $existsOther = PhanCongVaiTro::where('tai_khoan_id', $request->tai_khoan_id)
                ->where('hoi_dong_id', '!=', $request->hoi_dong_id)
                ->exists();
            if ($existsOther) {
                throw new \Exception('Giảng viên này đã được phân công vào một hội đồng khác.');
            }

            // Kiểm tra xem giảng viên có đang hướng dẫn đề tài không
            $giangVienCoDeTai = DeTai::where('giang_vien_id', $request->tai_khoan_id)
                ->where('trang_thai', '!=', DeTai::TRANG_THAI_KHONG_XAY_RA_GVHD)
                ->where('trang_thai', '!=', DeTai::TRANG_THAI_KHONG_XAY_RA_GVPB)
                ->exists();

            // Kiểm tra xem vai trò có phải là Trưởng tiểu ban hoặc Thư ký không
            $vaiTroKhongDuocPhanCong = VaiTro::whereIn('ten', ['Trưởng tiểu ban', 'Thư ký'])
                ->where('id', $request->vai_tro_id)
                ->exists();

            if ($giangVienCoDeTai && $vaiTroKhongDuocPhanCong) {
                throw new \Exception('Giảng viên đang hướng dẫn đề tài không thể được phân công làm Trưởng tiểu ban hoặc Thư ký.');
            }

            // Không cho trùng giảng viên trong cùng hội đồng
            $existsGV = PhanCongVaiTro::where('hoi_dong_id', $request->hoi_dong_id)
                ->where('tai_khoan_id', $request->tai_khoan_id)
                ->exists();
            if ($existsGV) {
                throw new \Exception('Giảng viên này đã được phân công vào hội đồng này!');
            }

            $data = $request->all();
            $thanhVienId = VaiTro::where('ten', 'Thành viên')->value('id');
            if ($request->vai_tro_id == $thanhVienId) {
                // Thành viên: chỉ kiểm tra trùng loại giảng viên
                $existsLoai = PhanCongVaiTro::where('hoi_dong_id', $request->hoi_dong_id)
                    ->where('vai_tro_id', $thanhVienId)
                    ->where('loai_giang_vien', $request->loai_giang_vien)
                    ->exists();
                if ($existsLoai) {
                    throw new \Exception('Loại giảng viên này đã được phân công cho một thành viên khác trong hội đồng!');
                }
            } elseif (in_array($request->vai_tro_id, [$truongTieuBanId, $thuKyId])) {
                // Trưởng tiểu ban, Thư ký: chỉ được 1 người duy nhất
                $existsRole = PhanCongVaiTro::where('hoi_dong_id', $request->hoi_dong_id)
                    ->where('vai_tro_id', $request->vai_tro_id)
                    ->exists();
                if ($existsRole) {
                    throw new \Exception('Vai trò này đã được phân công cho một giảng viên khác trong hội đồng!');
                }
            }

            // Tạo phân công vai trò
            $phanCongVaiTro = PhanCongVaiTro::create($data);

            // Nếu giảng viên có đề tài, thêm tất cả đề tài của họ vào hội đồng
            if ($giangVienCoDeTai) {
                $hoiDong = HoiDong::find($request->hoi_dong_id);
                $hoiDong->themDeTaiCuaGiangVien($request->tai_khoan_id);
            }

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
        $taiKhoans = TaiKhoan::where('vai_tro', 'giang_vien')->get();

        $usedVaiTroIds = PhanCongVaiTro::where('hoi_dong_id', $phanCongVaiTro->hoi_dong_id)
            ->where('id', '!=', $phanCongVaiTro->id)
            ->pluck('vai_tro_id')
            ->toArray();

        $vaiTros = VaiTro::whereNotIn('id', $usedVaiTroIds)->get();

        // Lấy danh sách giảng viên đang có đề tài
        $giangVienCoDeTai = DeTai::where('trang_thai', '!=', 4)
            ->pluck('giang_vien_id')
            ->unique()
            ->toArray();

        // Lấy danh sách vai trò không được phân công cho giảng viên có đề tài
        $vaiTroKhongDuocPhanCong = VaiTro::whereIn('ten', ['Trưởng tiểu ban', 'Thư ký'])
            ->pluck('id')
            ->toArray();

        $thanhVienId = VaiTro::where('ten', 'Thành viên')->value('id');

        $giangViensDaPhanCong = [];
        if ($phanCongVaiTro->hoi_dong_id) {
            $giangViensDaPhanCong = PhanCongVaiTro::where('hoi_dong_id', $phanCongVaiTro->hoi_dong_id)
                ->where('id', '!=', $phanCongVaiTro->id) // Loại trừ chính bản ghi đang sửa
                ->pluck('tai_khoan_id')
                ->toArray();
        }

        return view('admin.phan-cong-hoi-dong.edit', compact(
            'phanCongVaiTro', 
            'hoiDongs', 
            'taiKhoans', 
            'vaiTros',
            'giangVienCoDeTai',
            'vaiTroKhongDuocPhanCong',
            'thanhVienId',
            'giangViensDaPhanCong'
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

            // Kiểm tra trùng vai trò Giảng Viên Phản Biện hoặc Giảng Viên Khác trong cùng hội đồng
            $phanBienId = VaiTro::where('ten', 'Giảng Viên Phản Biện')->value('id');
            $khacId = VaiTro::where('ten', 'Giảng Viên Khác')->value('id');

            if (in_array($request->vai_tro_id, [$phanBienId, $khacId])) {
                $exists = PhanCongVaiTro::where('hoi_dong_id', $request->hoi_dong_id)
                    ->where('vai_tro_id', $request->vai_tro_id)
                    ->exists();
                if ($exists) {
                    throw new \Exception('Vai trò này đã được phân công cho một giảng viên khác trong hội đồng!');
                }
            }

            // Không cho giảng viên đã ở hội đồng khác được phân công tiếp (trừ chính bản ghi đang sửa)
            $existsOther = PhanCongVaiTro::where('tai_khoan_id', $request->tai_khoan_id)
                ->where('hoi_dong_id', '!=', $request->hoi_dong_id)
                ->where('id', '!=', $phanCongVaiTro->id)
                ->exists();
            if ($existsOther) {
                throw new \Exception('Giảng viên này đã được phân công vào một hội đồng khác.');
            }

            // Kiểm tra xem giảng viên có đang hướng dẫn đề tài không
            $giangVienCoDeTai = DeTai::where('giang_vien_id', $request->tai_khoan_id)
                ->where('trang_thai', '!=', DeTai::TRANG_THAI_KHONG_XAY_RA_GVHD)
                ->where('trang_thai', '!=', DeTai::TRANG_THAI_KHONG_XAY_RA_GVPB)
                ->exists();

            // Kiểm tra xem vai trò có phải là Trưởng tiểu ban hoặc Thư ký không
            $vaiTroKhongDuocPhanCong = VaiTro::whereIn('ten', ['Trưởng tiểu ban', 'Thư ký'])
                ->where('id', $request->vai_tro_id)
                ->exists();

            if ($giangVienCoDeTai && $vaiTroKhongDuocPhanCong) {
                throw new \Exception('Giảng viên đang hướng dẫn đề tài không thể được phân công làm Trưởng tiểu ban hoặc Thư ký.');
            }

            // Không cho trùng giảng viên trong cùng hội đồng
            $existsGV = PhanCongVaiTro::where('hoi_dong_id', $request->hoi_dong_id)
                ->where('tai_khoan_id', $request->tai_khoan_id)
                ->exists();
            if ($existsGV) {
                throw new \Exception('Giảng viên này đã được phân công vào hội đồng này!');
            }

            $data = $request->all();
            $thanhVienId = VaiTro::where('ten', 'Thành viên')->value('id');
            if ($request->vai_tro_id == $thanhVienId) {
                // Thành viên: chỉ kiểm tra trùng loại giảng viên
                $existsLoai = PhanCongVaiTro::where('hoi_dong_id', $request->hoi_dong_id)
                    ->where('vai_tro_id', $thanhVienId)
                    ->where('loai_giang_vien', $request->loai_giang_vien)
                    ->exists();
                if ($existsLoai) {
                    throw new \Exception('Loại giảng viên này đã được phân công cho một thành viên khác trong hội đồng!');
                }
            } elseif (in_array($request->vai_tro_id, [$truongTieuBanId, $thuKyId])) {
                // Trưởng tiểu ban, Thư ký: chỉ được 1 người duy nhất
                $existsRole = PhanCongVaiTro::where('hoi_dong_id', $request->hoi_dong_id)
                    ->where('vai_tro_id', $request->vai_tro_id)
                    ->exists();
                if ($existsRole) {
                    throw new \Exception('Vai trò này đã được phân công cho một giảng viên khác trong hội đồng!');
                }
            }

            // Cập nhật phân công vai trò
            $phanCongVaiTro->update($data);

            // Nếu giảng viên có đề tài và đã thay đổi hội đồng, thêm tất cả đề tài của họ vào hội đồng mới
            if ($giangVienCoDeTai && $request->hoi_dong_id != $phanCongVaiTro->hoi_dong_id) {
                $hoiDong = HoiDong::find($request->hoi_dong_id);
                $hoiDong->themDeTaiCuaGiangVien($request->tai_khoan_id);
            }

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