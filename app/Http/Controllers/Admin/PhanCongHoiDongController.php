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
        // Only show council assignments (no topic grading entries)
        $phanCongVaiTros = PhanCongVaiTro::with(['hoiDong','taiKhoan','vaiTro'])
            ->whereNull('de_tai_id')
            ->orderBy('created_at','desc')
            ->paginate(10);
            
        // Lấy tất cả phân công hội đồng để hoán đổi (không paginate)
        $allPhanCongVaiTros = PhanCongVaiTro::with(['hoiDong','taiKhoan','vaiTro'])
            ->whereNull('de_tai_id')
            ->get();

        $giangVienDaPhanCongIds = PhanCongVaiTro::pluck('tai_khoan_id')->unique()->toArray();
        $taiKhoansChuaPhanCong   = TaiKhoan::where('vai_tro','giang_vien')
            ->whereNotIn('id',$giangVienDaPhanCongIds)
            ->get();

        $hoiDongs = HoiDong::with([
            'chiTietBaoCaos.deTai',
            'chiTietBaoCaos.deTai.giangVien',
            'phanCongVaiTros.taiKhoan',
            'phanCongVaiTros.vaiTro',
        ])->get();

        // Tạo mảng theo hội đồng và vai trò
        $dsGiangVienThayThe = [];
        foreach ($hoiDongs as $hd) {
            foreach (['Trưởng tiểu ban', 'Thư ký'] as $vaiTro) {
                // Lấy id vai trò
                $vaiTroId = VaiTro::where('ten', $vaiTro)->value('id');
                // Giảng viên trong hội đồng này, chưa giữ vai trò đó
                $daPhanCongIds = PhanCongVaiTro::where('hoi_dong_id', $hd->id)
                    ->where('vai_tro_id', $vaiTroId)
                    ->pluck('tai_khoan_id')
                    ->toArray();
                $dsGV = TaiKhoan::where('vai_tro', 'giang_vien')
                    ->whereNotIn('id', $daPhanCongIds)
                    ->get(['id', 'ten']);
                $dsGiangVienThayThe[$hd->id][$vaiTro] = $dsGV->toArray();
            }
        }

        return view('admin.phan-cong-hoi-dong.index', compact(
            'phanCongVaiTros','allPhanCongVaiTros','taiKhoansChuaPhanCong','hoiDongs','dsGiangVienThayThe'
        ));        
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
        
        $pc1 = PhanCongVaiTro::with(['hoiDong', 'vaiTro', 'taiKhoan'])->findOrFail($request->phan_cong_id_1);
        $pc2 = PhanCongVaiTro::with(['hoiDong', 'vaiTro', 'taiKhoan'])->findOrFail($request->phan_cong_id_2);
        
        // Kiểm tra cả hai phải là phân công hội đồng (không phải phân công chấm đề tài)
        if ($pc1->de_tai_id !== null || $pc2->de_tai_id !== null) {
            return back()->withErrors(['error' => 'Chỉ được hoán đổi giữa các phân công hội đồng, không được hoán đổi với phân công chấm đề tài!']);
        }
        
        $hd1 = $pc1->hoiDong;
        $hd2 = $pc2->hoiDong;
        if (!$hd1 || !$hd2) {
            return back()->withErrors(['error' => 'Không tìm thấy hội đồng!']);
        }
        
        // Kiểm tra không được hoán đổi trong cùng hội đồng
        if ($hd1->id === $hd2->id) {
            return back()->withErrors(['error' => 'Không thể hoán đổi trong cùng một hội đồng!']);
        }
        
        // Kiểm tra cùng đợt báo cáo
        if ($hd1->dot_bao_cao_id !== $hd2->dot_bao_cao_id) {
            return back()->withErrors(['error' => 'Chỉ được hoán đổi giữa các hội đồng cùng đợt báo cáo!']);
        }
        
        // Kiểm tra cùng thời gian bắt đầu
        if ($hd1->thoi_gian_bat_dau != $hd2->thoi_gian_bat_dau) {
            return back()->withErrors(['error' => 'Chỉ được hoán đổi giữa các hội đồng cùng thời gian!']);
        }
        
        // Kiểm tra vai trò quan trọng (Trưởng tiểu ban, Thư ký) chỉ được 1 người
        $truongTieuBanId = VaiTro::where('ten', 'Trưởng tiểu ban')->value('id');
        $thuKyId = VaiTro::where('ten', 'Thư ký')->value('id');
        $thanhVienId = VaiTro::where('ten', 'Thành viên')->value('id');
        
        // Kiểm tra không cho phép hoán đổi giữa Trưởng tiểu ban và Thư ký
        if (
            ($pc1->vai_tro_id == $truongTieuBanId && $pc2->vai_tro_id == $thuKyId) ||
            ($pc1->vai_tro_id == $thuKyId && $pc2->vai_tro_id == $truongTieuBanId)
        ) {
            return back()->withErrors(['error' => 'Không thể hoán đổi trực tiếp giữa vai trò Trưởng tiểu ban và Thư ký!']);
        }
        
        // Kiểm tra trùng lặp vai trò quan trọng sau khi hoán đổi
        // Chỉ kiểm tra nếu cả hai đều có vai trò quan trọng khác nhau
        if (in_array($pc1->vai_tro_id, [$truongTieuBanId, $thuKyId]) && 
            in_array($pc2->vai_tro_id, [$truongTieuBanId, $thuKyId]) &&
            $pc1->vai_tro_id != $pc2->vai_tro_id) {
            
            // Nếu pc1 có vai trò quan trọng và hội đồng đích đã có vai trò này (trừ pc2 nếu pc2 cùng vai trò)
            $existsInTargetHoiDong = PhanCongVaiTro::where('hoi_dong_id', $hd2->id)
                ->where('vai_tro_id', $pc1->vai_tro_id)
                ->where('id', '!=', $pc2->id)
                ->exists();
            if ($existsInTargetHoiDong) {
                return back()->withErrors(['error' => 'Hội đồng đích đã có vai trò ' . $pc1->vaiTro->ten . '!']);
            }
            
            // Nếu pc2 có vai trò quan trọng và hội đồng nguồn đã có vai trò này (trừ pc1 nếu pc1 cùng vai trò)
            $existsInSourceHoiDong = PhanCongVaiTro::where('hoi_dong_id', $hd1->id)
                ->where('vai_tro_id', $pc2->vai_tro_id)
                ->where('id', '!=', $pc1->id)
                ->exists();
            if ($existsInSourceHoiDong) {
                return back()->withErrors(['error' => 'Hội đồng nguồn đã có vai trò ' . $pc2->vaiTro->ten . '!']);
            }
        }
        
        // Kiểm tra loại giảng viên (hướng dẫn/phản biện)
        // Lấy loại giảng viên của pc1 và pc2
        $loaiGiangVien1 = $pc1->loai_giang_vien;
        $loaiGiangVien2 = $pc2->loai_giang_vien;
        
        // Nếu pc1 là giảng viên hướng dẫn hoặc phản biện
        if ($loaiGiangVien1 && in_array($loaiGiangVien1, ['hướng dẫn', 'phản biện'])) {
            // Kiểm tra hội đồng đích đã có giảng viên cùng loại chưa (trừ pc2 nếu pc2 cùng loại)
            $existsLoaiInTargetHoiDong = PhanCongVaiTro::where('hoi_dong_id', $hd2->id)
                ->where('loai_giang_vien', $loaiGiangVien1)
                ->where('id', '!=', $pc2->id)
                ->exists();
            if ($existsLoaiInTargetHoiDong) {
                return back()->withErrors(['error' => "Hội đồng đích đã có giảng viên {$loaiGiangVien1}! Không thể hoán đổi."]);
            }
        }
        
        // Nếu pc2 là giảng viên hướng dẫn hoặc phản biện
        if ($loaiGiangVien2 && in_array($loaiGiangVien2, ['hướng dẫn', 'phản biện'])) {
            // Kiểm tra hội đồng nguồn đã có giảng viên cùng loại chưa (trừ pc1 nếu pc1 cùng loại)
            $existsLoaiInSourceHoiDong = PhanCongVaiTro::where('hoi_dong_id', $hd1->id)
                ->where('loai_giang_vien', $loaiGiangVien2)
                ->where('id', '!=', $pc1->id)
                ->exists();
            if ($existsLoaiInSourceHoiDong) {
                return back()->withErrors(['error' => "Hội đồng nguồn đã có giảng viên {$loaiGiangVien2}! Không thể hoán đổi."]);
            }
        }
        
        DB::beginTransaction();
        try {
            $hoiDong1 = $pc1->hoi_dong_id;
            $hoiDong2 = $pc2->hoi_dong_id;
            $vaiTro1 = $pc1->vai_tro_id;
            $vaiTro2 = $pc2->vai_tro_id;
            
            // Hoán đổi cả hội đồng và vai trò
            $pc1->hoi_dong_id = $hoiDong2;
            $pc1->vai_tro_id = $vaiTro2;
            $pc2->hoi_dong_id = $hoiDong1;
            $pc2->vai_tro_id = $vaiTro1;
            
            $pc1->save();
            $pc2->save();
            DB::commit();
            
            // Lấy tên vai trò mới sau khi hoán đổi
            $vaiTro1Ten = VaiTro::find($vaiTro1)->ten;
            $vaiTro2Ten = VaiTro::find($vaiTro2)->ten;
            
            $successMessage = "Hoán đổi giảng viên và vai trò thành công! ";
            $successMessage .= "{$pc1->taiKhoan->ten} (vai trò: {$vaiTro1Ten} → {$vaiTro2Ten}" . ($pc1->loai_giang_vien ? " - {$pc1->loai_giang_vien}" : "") . ") từ {$hd1->ten} sang {$hd2->ten}. ";
            $successMessage .= "{$pc2->taiKhoan->ten} (vai trò: {$vaiTro2Ten} → {$vaiTro1Ten}" . ($pc2->loai_giang_vien ? " - {$pc2->loai_giang_vien}" : "") . ") từ {$hd2->ten} sang {$hd1->ten}.";
            
            return back()->with('success', $successMessage);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Có lỗi xảy ra khi hoán đổi: ' . $e->getMessage()]);
        }
    }

    public function addCham(Request $request)
    {
        $request->validate([
            'hoi_dong_id'  => 'required|exists:hoi_dongs,id',
            'de_tai_id'    => 'required|exists:de_tais,id',
            'tai_khoan_id' => 'required|exists:tai_khoans,id',
        ]);

        // Tìm vai_tro_id và loai_giang_vien gốc của giảng viên trong hội đồng
        $phanCongGoc = PhanCongVaiTro::where('hoi_dong_id', $request->hoi_dong_id)
            ->where('tai_khoan_id', $request->tai_khoan_id)
            ->whereNull('de_tai_id')
            ->first();

        $vai_tro_id = $phanCongGoc ? $phanCongGoc->vai_tro_id : VaiTro::where('ten','Thành viên')->value('id');
        $loai_giang_vien = $phanCongGoc ? $phanCongGoc->loai_giang_vien : null;

        // Kiểm tra đã phân công chấm đề tài này cho giảng viên này chưa
        $exists = PhanCongVaiTro::where([
            ['hoi_dong_id',  $request->hoi_dong_id],
            ['de_tai_id',    $request->de_tai_id],
            ['tai_khoan_id', $request->tai_khoan_id],
            ['vai_tro_id',   $vai_tro_id],
        ])->exists();

        if ($exists) {
            return back()->withErrors(['error'=>'Giảng viên đã được phân công chấm đề tài!']);
        }

        // Tạo bản ghi phân công chấm mới (phải giữ nguyên loai_giang_vien)
        PhanCongVaiTro::create([
            'hoi_dong_id'    => $request->hoi_dong_id,
            'de_tai_id'      => $request->de_tai_id,
            'tai_khoan_id'   => $request->tai_khoan_id,
            'vai_tro_id'     => $vai_tro_id,
            'loai_giang_vien'=> $loai_giang_vien,
        ]);

        return redirect()
            ->route('admin.phan-cong-hoi-dong.index')
            ->with('success','Phân công chấm thành công!')
            ->with('openModal',$request->hoi_dong_id);
    }

    public function replaceAndDelete(Request $request)
    {
        $request->validate([
            'phan_cong_id' => 'required|exists:phan_cong_vai_tros,id',
            'tai_khoan_id' => 'required|exists:tai_khoans,id',
            'vai_tro'      => 'required',
            'hoi_dong_id'  => 'required|exists:hoi_dongs,id',
        ]);
        $phanCong = PhanCongVaiTro::findOrFail($request->phan_cong_id);
        $vaiTroId = VaiTro::where('ten', $request->vai_tro)->value('id');
    
        // Kiểm tra giảng viên đã được phân công ở hội đồng khác chưa (trừ chính bản ghi bị thay)
        $existsGV = PhanCongVaiTro::where('tai_khoan_id', $request->tai_khoan_id)
            ->where('id', '!=', $phanCong->id)
            ->exists();
        if ($existsGV) {
            return back()
                ->withErrors(['tai_khoan_id' => 'Giảng viên này đã được phân công vào hội đồng khác! Mỗi giảng viên chỉ được phân công vào 1 hội đồng duy nhất.'])
                ->withInput()
                ->with('openReplaceModal', [
                    'phan_cong_id' => $request->phan_cong_id,
                    'vai_tro' => $request->vai_tro,
                    'hoi_dong_id' => $request->hoi_dong_id
                ]);
        }
    
        // Kiểm tra giảng viên đã được phân công vào hội đồng này chưa (trừ chính bản ghi bị thay)
        $existsGVInSameHoiDong = PhanCongVaiTro::where('tai_khoan_id', $request->tai_khoan_id)
            ->where('hoi_dong_id', $request->hoi_dong_id)
            ->where('id', '!=', $phanCong->id)
            ->exists();
        if ($existsGVInSameHoiDong) {
            return back()
                ->withErrors(['tai_khoan_id' => 'Giảng viên này đã được phân công vào hội đồng này!'])
                ->withInput()
                ->with('openReplaceModal', [
                    'phan_cong_id' => $request->phan_cong_id,
                    'vai_tro' => $request->vai_tro,
                    'hoi_dong_id' => $request->hoi_dong_id
                ]);
        }
    
        DB::beginTransaction();
        try {
            PhanCongVaiTro::create([
                'hoi_dong_id'  => $request->hoi_dong_id,
                'tai_khoan_id' => $request->tai_khoan_id,
                'vai_tro_id'   => $vaiTroId,
            ]);
            $phanCong->delete();
            DB::commit();
            return redirect()->route('admin.phan-cong-hoi-dong.index')
                ->with('success', 'Đã thay thế và xóa thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }    
}
