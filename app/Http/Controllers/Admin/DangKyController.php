<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DangKyGiangVienHuongDan;
use App\Models\SinhVien;
use App\Models\TaiKhoan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DangKyController extends Controller
{
    public function index(Request $request)
    {
        $query = DangKyGiangVienHuongDan::with(['sinhVien', 'giangVien']);

        // Lọc theo sinh viên
        if ($request->filled('sinh_vien_id')) {
            $query->where('sinh_vien_id', $request->sinh_vien_id);
        }

        // Lọc theo giảng viên
        if ($request->filled('giang_vien_id')) {
            $query->where('giang_vien_id', $request->giang_vien_id);
        }

        $dangKys = $query->latest()->paginate(10);

        // Lấy danh sách sinh viên cho dropdown lọc
        $sinhViens = SinhVien::all();

        // Lấy danh sách giảng viên cho dropdown lọc
        $giangViens = TaiKhoan::where('vai_tro', 'giang_vien')->get();

        return view('admin.dang-ky.index', compact('dangKys', 'sinhViens', 'giangViens'));
    }

    public function create()
    {
        // Admin có thể chọn bất kỳ sinh viên nào chưa có đăng ký
        $sinhViens = SinhVien::whereDoesntHave('dangKyGiangVienHuongDan')->get();

        // Admin có thể chọn bất kỳ giảng viên nào
        $giangViens = TaiKhoan::where('vai_tro', 'giang_vien')->get();

        return view('admin.dang-ky.create', compact('sinhViens', 'giangViens'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sinh_vien_id' => 'required|exists:sinh_viens,id|unique:dang_ky_giang_vien_huong_dans,sinh_vien_id',
            'giang_vien_id' => 'required|exists:tai_khoans,id',
        ], [
            'sinh_vien_id.required' => 'Vui lòng chọn sinh viên.',
            'sinh_vien_id.exists' => 'Sinh viên được chọn không tồn tại.',
            'sinh_vien_id.unique' => 'Sinh viên này đã có đăng ký hướng dẫn.',
            'giang_vien_id.required' => 'Vui lòng chọn giảng viên.',
            'giang_vien_id.exists' => 'Giảng viên được chọn không tồn tại.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            DangKyGiangVienHuongDan::create([
                'sinh_vien_id' => $request->sinh_vien_id,
                'giang_vien_id' => $request->giang_vien_id,
                'trang_thai' => 'da_duyet' // Admin thêm mặc định là đã duyệt
            ]);

            DB::commit();
            return redirect()->route('admin.dang-ky.index')
                ->with('success', 'Thêm đăng ký thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi thêm đăng ký: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit(DangKyGiangVienHuongDan $dangKy)
    {
        // Lấy danh sách sinh viên (bao gồm cả sinh viên hiện tại)
        $sinhViens = SinhVien::whereDoesntHave('dangKyGiangVienHuongDan')
            ->orWhere('id', $dangKy->sinh_vien_id)
            ->get();

        // Lấy danh sách giảng viên
        $giangViens = TaiKhoan::where('vai_tro', 'giang_vien')->get();

        $trangThais = [
            'cho_duyet' => 'Chờ duyệt',
            'da_duyet' => 'Đã duyệt',
            'tu_choi' => 'Từ chối',
        ];

        return view('admin.dang-ky.edit', compact('dangKy', 'sinhViens', 'giangViens', 'trangThais'));
    }

    public function update(Request $request, DangKyGiangVienHuongDan $dangKy)
    {
        $validator = Validator::make($request->all(), [
            'sinh_vien_id' => 'required|exists:sinh_viens,id|unique:dang_ky_giang_vien_huong_dans,sinh_vien_id,' . $dangKy->id,
            'giang_vien_id' => 'required|exists:tai_khoans,id',
            'trang_thai' => 'required|in:cho_duyet,da_duyet,tu_choi',
        ], [
            'sinh_vien_id.required' => 'Vui lòng chọn sinh viên.',
            'sinh_vien_id.exists' => 'Sinh viên được chọn không tồn tại.',
            'sinh_vien_id.unique' => 'Sinh viên này đã có đăng ký hướng dẫn khác.',
            'giang_vien_id.required' => 'Vui lòng chọn giảng viên.',
            'giang_vien_id.exists' => 'Giảng viên được chọn không tồn tại.',
            'trang_thai.required' => 'Vui lòng chọn trạng thái.',
            'trang_thai.in' => 'Trạng thái không hợp lệ.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $dangKy->update([
                'sinh_vien_id' => $request->sinh_vien_id,
                'giang_vien_id' => $request->giang_vien_id,
                'trang_thai' => $request->trang_thai,
            ]);

            DB::commit();
            return redirect()->route('admin.dang-ky.index')
                ->with('success', 'Cập nhật đăng ký thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi cập nhật đăng ký: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(DangKyGiangVienHuongDan $dangKy)
    {
        try {
            $dangKy->delete();
            return redirect()->route('admin.dang-ky.index')
                ->with('success', 'Xóa đăng ký thành công!');
        } catch (\Exception $e) {
            return redirect()->route('admin.dang-ky.index')
                ->with('error', 'Không thể xóa đăng ký này do ràng buộc dữ liệu liên quan.');
        }
    }
} 