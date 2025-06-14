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
            
        return view('admin.phan-cong-hoi-dong.index', compact('phanCongVaiTros'));
    }

    public function create(Request $request)
    {
        $hoiDongs = HoiDong::all();
        $taiKhoans = TaiKhoan::where('vai_tro', 'giang_vien')->get();
        $vaiTros = VaiTro::all();

        // Lấy danh sách giảng viên đang có đề tài
        $giangVienCoDeTai = DeTai::where('trang_thai', '!=', 4) // Không tính các đề tài đã bị từ chối
            ->pluck('giang_vien_id')
            ->unique()
            ->toArray();

        // Lấy danh sách vai trò không được phân công cho giảng viên có đề tài
        $vaiTroKhongDuocPhanCong = VaiTro::whereIn('ten', ['Trưởng tiểu ban', 'Thư ký'])
            ->pluck('id')
            ->toArray();

        $selectedHoiDong = $request->get('hoi_dong_id');
        if ($selectedHoiDong) {
            $usedVaiTroIds = PhanCongVaiTro::where('hoi_dong_id', $selectedHoiDong)->pluck('vai_tro_id')->toArray();
            $vaiTros = VaiTro::whereNotIn('id', $usedVaiTroIds)->get();
        }

        return view('admin.phan-cong-hoi-dong.create', compact(
            'hoiDongs', 
            'taiKhoans', 
            'vaiTros', 
            'selectedHoiDong',
            'giangVienCoDeTai',
            'vaiTroKhongDuocPhanCong'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'hoi_dong_id' => 'required|exists:hoi_dongs,id',
            'tai_khoan_id' => 'required|exists:tai_khoans,id',
            'vai_tro_id' => 'required|exists:vai_tros,id'
        ]);

        try {
            DB::beginTransaction();

            // Kiểm tra xem giảng viên đã được phân công vào hội đồng này chưa
            $exists = PhanCongVaiTro::where('hoi_dong_id', $request->hoi_dong_id)
                ->where('tai_khoan_id', $request->tai_khoan_id)
                ->exists();

            if ($exists) {
                throw new \Exception('Giảng viên này đã được phân công vào hội đồng.');
            }

            // Kiểm tra xem vai trò đã được phân công cho hội đồng chưa
            $existsRole = PhanCongVaiTro::where('hoi_dong_id', $request->hoi_dong_id)
                ->where('vai_tro_id', $request->vai_tro_id)
                ->exists();

            if ($existsRole) {
                throw new \Exception('Vai trò này đã được phân công cho hội đồng.');
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

            // Tạo phân công vai trò
            $phanCongVaiTro = PhanCongVaiTro::create($request->all());

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

        return view('admin.phan-cong-hoi-dong.edit', compact(
            'phanCongVaiTro', 
            'hoiDongs', 
            'taiKhoans', 
            'vaiTros',
            'giangVienCoDeTai',
            'vaiTroKhongDuocPhanCong'
        ));
    }

    public function update(Request $request, PhanCongVaiTro $phanCongVaiTro)
    {
        $request->validate([
            'hoi_dong_id' => 'required|exists:hoi_dongs,id',
            'tai_khoan_id' => 'required|exists:tai_khoans,id',
            'vai_tro_id' => 'required|exists:vai_tros,id'
        ]);

        try {
            DB::beginTransaction();

            // Kiểm tra xem giảng viên đã được phân công vào hội đồng khác chưa
            $exists = PhanCongVaiTro::where('hoi_dong_id', $request->hoi_dong_id)
                ->where('tai_khoan_id', $request->tai_khoan_id)
                ->where('id', '!=', $phanCongVaiTro->id)
                ->exists();

            if ($exists) {
                throw new \Exception('Giảng viên này đã được phân công vào hội đồng.');
            }

            // Kiểm tra xem vai trò đã được phân công cho hội đồng chưa
            $existsRole = PhanCongVaiTro::where('hoi_dong_id', $request->hoi_dong_id)
                ->where('vai_tro_id', $request->vai_tro_id)
                ->where('id', '!=', $phanCongVaiTro->id)
                ->exists();

            if ($existsRole) {
                throw new \Exception('Vai trò này đã được phân công cho hội đồng.');
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

            // Cập nhật phân công vai trò
            $phanCongVaiTro->update($request->all());

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
} 