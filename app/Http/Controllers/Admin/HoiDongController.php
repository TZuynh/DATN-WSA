<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HoiDong;
use Illuminate\Support\Facades\DB;

class HoiDongController extends Controller
{
    /**
     * Hiển thị danh sách hội đồng
     */
    public function index()
    {
        $hoiDongs = HoiDong::orderBy('created_at', 'desc')->get();
        return view('admin.hoidong.index', compact('hoiDongs'));
    }

    /**
     * Lưu hội đồng mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'ma_hoi_dong' => 'required|string|max:255|unique:hoi_dongs,ma_hoi_dong',
            'ten' => 'required|string|max:255',
        ]);

        try {
            DB::beginTransaction();
            
            HoiDong::create([
                'ma_hoi_dong' => $request->ma_hoi_dong,
                'ten' => $request->ten,
            ]);

            DB::commit();
            return redirect()->route('admin.hoidong.index')->with('success', 'Thêm hội đồng thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Hiển thị form sửa hội đồng
     */
    public function edit($id)
    {
        $hoiDong = HoiDong::findOrFail($id);
        return view('admin.hoidong.edit', compact('hoiDong'));
    }

    /**
     * Cập nhật hội đồng
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'ma_hoi_dong' => 'required|string|max:255|unique:hoi_dongs,ma_hoi_dong,' . $id,
            'ten' => 'required|string|max:255',
        ]);

        try {
            DB::beginTransaction();
            
            $hoiDong = HoiDong::findOrFail($id);
            $hoiDong->update([
                'ma_hoi_dong' => $request->ma_hoi_dong,
                'ten' => $request->ten,
            ]);

            DB::commit();
            return redirect()->route('admin.hoidong.index')->with('success', 'Cập nhật hội đồng thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Xóa hội đồng
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            
            $hoiDong = HoiDong::findOrFail($id);
            $hoiDong->delete();

            DB::commit();
            return redirect()->route('admin.hoidong.index')->with('success', 'Xóa hội đồng thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
} 