<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use App\Models\DangKyGiangVienHuongDan;
use App\Models\SinhVien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DangKyController extends Controller
{
    public function index(Request $request)
    {
        $query = DangKyGiangVienHuongDan::with(['sinhVien', 'giangVien'])
            ->where('giang_vien_id', auth()->id());

        // Nếu có chọn sinh viên để lọc
        if ($request->filled('sinh_vien_id')) {
            $query->where('sinh_vien_id', $request->sinh_vien_id);
        }

        $dangKys = $query->latest()->paginate(10);

        // Lấy danh sách sinh viên đã đăng ký với giảng viên này để hiển thị ở dropdown lọc
        $sinhViens = \App\Models\SinhVien::whereIn('id', function ($q) {
            $q->select('sinh_vien_id')
                ->from('dang_ky_giang_vien_huong_dans')
                ->where('giang_vien_id', auth()->id());
        })->get();

        // Lấy danh sách nhóm của giảng viên
        $nhoms = \App\Models\Nhom::with('sinhViens')
            ->where('giang_vien_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('giangvien.dang-ky.index', compact('dangKys', 'sinhViens', 'nhoms'));
    }


    public function create()
    {
        // Only get students who don't have a registration with the currently logged in lecturer
        $sinhViens = SinhVien::whereDoesntHave('dangKyGiangVienHuongDan', function($query) {
            $query->where('giang_vien_id', auth()->id());
        })->get();

        // Get only the current logged-in lecturer
        $giangViens = \App\Models\TaiKhoan::where('id', auth()->id())->get();

        return view('giangvien.dang-ky.create', compact('sinhViens', 'giangViens'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sinh_vien_ids' => 'required|array',
            'sinh_vien_ids.*' => 'exists:sinh_viens,id',
        ], [
            'sinh_vien_ids.required' => 'Vui lòng chọn ít nhất một sinh viên.',
            'sinh_vien_ids.array' => 'Dữ liệu sinh viên không hợp lệ.',
            'sinh_vien_ids.*.exists' => 'Một hoặc nhiều sinh viên được chọn không tồn tại.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Always use the currently logged in lecturer
        $giangVienId = auth()->id();

        $sinhVienIds = SinhVien::whereIn('id', $request->sinh_vien_ids)
            ->whereDoesntHave('dangKyGiangVienHuongDan', function($query) use ($giangVienId) {
                $query->where('giang_vien_id', $giangVienId);
            })
            ->pluck('id');

        if ($sinhVienIds->isEmpty()) {
            return redirect()->back()
                ->with('error', 'Các sinh viên được chọn đều đã có đăng ký hoặc không hợp lệ.')
                ->withInput();
        }

        DB::beginTransaction();
        try {
            foreach ($sinhVienIds as $sinhVienId) {
                DangKyGiangVienHuongDan::create([
                    'sinh_vien_id' => $sinhVienId,
                    'giang_vien_id' => $giangVienId,
                    'trang_thai' => 'da_duyet'
                ]);
            }

            DB::commit();
            return redirect()->route('giangvien.dang-ky.index')
                ->with('success', 'Thêm đăng ký thành công cho ' . $sinhVienIds->count() . ' sinh viên.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi thêm đăng ký: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit(DangKyGiangVienHuongDan $dangKy)
    {
        // Make sure this lecturer can only edit their own registrations
        if ($dangKy->giang_vien_id !== auth()->id()) {
            return redirect()->route('giangvien.dang-ky.index')
                ->with('error', 'Bạn không có quyền chỉnh sửa đăng ký này.');
        }

        $sinhViens = SinhVien::whereDoesntHave('dangKyGiangVienHuongDan', function($query) use ($dangKy) {
            $query->where('id', '!=', $dangKy->id);
        })
            ->orWhere('id', $dangKy->sinh_vien_id)
            ->get();

        $trangThais = [
            'da_duyet' => 'Đã duyệt',
            'tu_choi' => 'Từ chối',
        ];

        return view('giangvien.dang-ky.edit', compact('dangKy', 'sinhViens', 'trangThais'));
    }

    public function update(Request $request, DangKyGiangVienHuongDan $dangKy)
    {
        // Make sure this lecturer can only update their own registrations
        if ($dangKy->giang_vien_id !== auth()->id()) {
            return redirect()->route('giangvien.dang-ky.index')
                ->with('error', 'Bạn không có quyền cập nhật đăng ký này.');
        }

        $validator = Validator::make($request->all(), [
            'sinh_vien_id' => 'required|exists:sinh_viens,id',
            'trang_thai' => 'required|in:da_duyet,tu_choi',
        ], [
            'sinh_vien_id.required' => 'Vui lòng chọn sinh viên.',
            'sinh_vien_id.exists' => 'Sinh viên được chọn không tồn tại.',
            'trang_thai.required' => 'Vui lòng chọn trạng thái.',
            'trang_thai.in' => 'Trạng thái không hợp lệ.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Update conflict check
        $conflictExists = DangKyGiangVienHuongDan::where('sinh_vien_id', $request->sinh_vien_id)
            ->where('giang_vien_id', auth()->id())
            ->where('id', '!=', $dangKy->id)
            ->exists();

        if ($conflictExists) {
            return redirect()->back()
                ->with('error', 'Sinh viên này đã có đăng ký hướng dẫn với bạn.')
                ->withInput();
        }

        $dangKy->update([
            'sinh_vien_id' => $request->sinh_vien_id,
            'trang_thai' => $request->trang_thai,
        ]);

        return redirect()->route('giangvien.dang-ky.index')
            ->with('success', 'Cập nhật đăng ký thành công!');
    }

    public function destroy(DangKyGiangVienHuongDan $dangKy)
    {
        // Make sure this lecturer can only delete their own registrations
        if ($dangKy->giang_vien_id !== auth()->id()) {
            return redirect()->route('giangvien.dang-ky.index')
                ->with('error', 'Bạn không có quyền xóa đăng ký này.');
        }

        try {
            $dangKy->delete();
            return redirect()->route('giangvien.dang-ky.index')
                ->with('success', 'Xóa đăng ký thành công!');
        } catch (\Exception $e) {
            return redirect()->route('giangvien.dang-ky.index')
                ->with('error', 'Không thể xóa đăng ký này do ràng buộc dữ liệu liên quan.');
        }
    }
}
