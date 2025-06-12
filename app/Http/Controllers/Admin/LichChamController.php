<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LichCham;
use App\Models\HoiDong;
use App\Models\DotBaoCao;
use App\Models\Nhom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LichChamController extends Controller
{
    /**
     * Hiển thị danh sách lịch chấm
     */
    public function index()
    {
        $lichChams = LichCham::with(['hoiDong', 'dotBaoCao', 'nhom'])
            ->orderBy('lich_tao', 'desc')
            ->paginate(10);
            
        return view('admin.lich-cham.index', compact('lichChams'));
    }

    /**
     * Hiển thị form tạo lịch chấm mới
     */
    public function create()
    {
        $hoiDongs = HoiDong::all();
        $dotBaoCaos = DotBaoCao::all();
        $nhoms = Nhom::where('trang_thai', 'hoat_dong')
            ->select('id', 'ma_nhom', 'ten', 'giang_vien_id')
            ->with('giangVien:id,ten')
            ->get();
            
        return view('admin.lich-cham.create', compact('hoiDongs', 'dotBaoCaos', 'nhoms'));
    }

    /**
     * Lưu lịch chấm mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'hoi_dong_id' => 'required|exists:hoi_dongs,id',
            'dot_bao_cao_id' => 'required|exists:dot_bao_caos,id',
            'nhom_id' => 'required|exists:nhoms,id',
            'lich_tao' => 'required|date'
        ]);

        try {
            DB::beginTransaction();

            // Kiểm tra xung đột lịch chấm
            $xungDot = LichCham::where('hoi_dong_id', $request->hoi_dong_id)
                ->where('lich_tao', $request->lich_tao)
                ->exists();

            if ($xungDot) {
                throw new \Exception('Đã có lịch chấm cho hội đồng này vào thời gian này.');
            }

            LichCham::create($request->all());

            DB::commit();
            return redirect()->route('admin.lich-cham.index')
                ->with('success', 'Thêm lịch chấm thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Không thể thêm lịch chấm: ' . $e->getMessage()]);
        }
    }

    /**
     * Hiển thị form sửa lịch chấm
     */
    public function edit(LichCham $lichCham)
    {
        $hoiDongs = HoiDong::all();
        $dotBaoCaos = DotBaoCao::all();
        $nhoms = Nhom::where('trang_thai', 'hoat_dong')
            ->select('id', 'ma_nhom', 'ten', 'giang_vien_id')
            ->with('giangVien:id,ten')
            ->get();
            
        return view('admin.lich-cham.edit', compact('lichCham', 'hoiDongs', 'dotBaoCaos', 'nhoms'));
    }

    /**
     * Cập nhật lịch chấm
     */
    public function update(Request $request, LichCham $lichCham)
    {
        $request->validate([
            'hoi_dong_id' => 'required|exists:hoi_dongs,id',
            'dot_bao_cao_id' => 'required|exists:dot_bao_caos,id',
            'nhom_id' => 'required|exists:nhoms,id',
            'lich_tao' => 'required|date'
        ]);

        try {
            DB::beginTransaction();

            // Kiểm tra xung đột lịch chấm
            $xungDot = LichCham::where('hoi_dong_id', $request->hoi_dong_id)
                ->where('lich_tao', $request->lich_tao)
                ->where('id', '!=', $lichCham->id)
                ->exists();

            if ($xungDot) {
                throw new \Exception('Đã có lịch chấm cho hội đồng này vào thời gian này.');
            }

            $lichCham->update($request->all());

            DB::commit();
            return redirect()->route('admin.lich-cham.index')
                ->with('success', 'Cập nhật lịch chấm thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Không thể cập nhật lịch chấm: ' . $e->getMessage()]);
        }
    }

    /**
     * Xóa lịch chấm
     */
    public function destroy(LichCham $lichCham)
    {
        try {
            DB::beginTransaction();

            // Kiểm tra xem lịch chấm đã diễn ra chưa
            if (Carbon::parse($lichCham->lich_tao)->isPast()) {
                throw new \Exception('Không thể xóa lịch chấm đã diễn ra.');
            }

            $lichCham->delete();

            DB::commit();
            return redirect()->route('admin.lich-cham.index')
                ->with('success', 'Xóa lịch chấm thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.lich-cham.index')
                ->with('error', 'Không thể xóa lịch chấm: ' . $e->getMessage());
        }
    }
} 