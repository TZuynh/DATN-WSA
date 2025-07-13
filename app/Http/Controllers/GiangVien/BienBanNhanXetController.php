<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BienBanNhanXet;
use App\Models\BienBanCauTraLoi;
use App\Models\DeTai;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BienBanNhanXetController extends Controller
{
    public function create($deTaiId)
    {
        $deTai = DeTai::with('chiTietBaoCao.hoiDong.phanCongVaiTros.vaiTro')->findOrFail($deTaiId);
        $user = Auth::user();
        $isThuKy = $deTai->chiTietBaoCao && $deTai->chiTietBaoCao->hoiDong && $deTai->chiTietBaoCao->hoiDong->phanCongVaiTros->where('tai_khoan_id', $user->id)->where('vaiTro.ten', 'Thư ký')->count();
        if (!$isThuKy) abort(403, 'Bạn không phải là thư ký hội đồng này');
        $bienBan = \App\Models\BienBanNhanXet::where('ma_de_tai', $deTai->ma_de_tai)->first();
        if ($bienBan) {
            return redirect()->route('giangvien.bien-ban-nhan-xet.show', $deTai->id);
        }
        return view('giangvien.bien-ban-nhan-xet.create', compact('deTai'));
    }

    public function store(Request $request, $deTaiId)
    {
        $request->validate([
            'hinh_thuc' => 'nullable',
            'cap_thiet' => 'nullable',
            'muc_tieu' => 'nullable',
            'tai_lieu' => 'nullable',
            'phuong_phap' => 'nullable',
            'ket_qua' => 'required|in:Đạt,Không đạt',
            'qua_trinh_hoat_dong' => 'nullable',
            'cau_hoi' => 'required|array',
        ]);
        $deTai = DeTai::with('chiTietBaoCao')->findOrFail($deTaiId);
        $user = Auth::user();
        $hoiDongId = $deTai->chiTietBaoCao->hoi_dong_id;
        $dotBaoCaoId = $deTai->dot_bao_cao_id;
        $bienBan = BienBanNhanXet::create([
            'hoi_dong_id' => $hoiDongId,
            'dot_bao_cao_id' => $dotBaoCaoId,
            'ma_de_tai' => $deTai->ma_de_tai,
            'hinh_thuc' => $request->hinh_thuc,
            'cap_thiet' => $request->cap_thiet,
            'muc_tieu' => $request->muc_tieu,
            'tai_lieu' => $request->tai_lieu,
            'phuong_phap' => $request->phuong_phap,
            'ket_qua' => $request->ket_qua,
            'qua_trinh_hoat_dong' => $request->qua_trinh_hoat_dong,
        ]);
        foreach ($request->cau_hoi as $cauHoi) {
            if (trim($cauHoi)) {
                BienBanCauTraLoi::create([
                    'bien_ban_nhan_xet_id' => $bienBan->id,
                    'cau_hoi' => $cauHoi,
                ]);
            }
        }
        return redirect()->route('giangvien.bien-ban-nhan-xet.select-detai')->with('success', 'Lưu biên bản thành công!');
    }

    public function selectDeTai()
    {
        $user = auth()->user();
        Log::info('[BienBanNhanXetController@selectDeTai] user_id: ' . $user->id);
        $hoiDongIds = \App\Models\PhanCongVaiTro::where('tai_khoan_id', $user->id)
            ->whereHas('vaiTro', function($q) { $q->where('ten', 'Thư ký'); })
            ->pluck('hoi_dong_id');
        Log::info('[BienBanNhanXetController@selectDeTai] hoiDongIds: ' . json_encode($hoiDongIds));
        $deTaiIds = \App\Models\ChiTietDeTaiBaoCao::whereIn('hoi_dong_id', $hoiDongIds)->pluck('de_tai_id');
        Log::info('[BienBanNhanXetController@selectDeTai] deTaiIds: ' . json_encode($deTaiIds));
        $deTais = \App\Models\DeTai::with(['nhoms.sinhViens', 'dotBaoCao.hocKy', 'giangVien'])
            ->whereIn('id', $deTaiIds)
            ->orderBy('created_at', 'desc')
            ->get();
        Log::info('[BienBanNhanXetController@selectDeTai] deTais count: ' . $deTais->count());
        return view('giangvien.bien-ban-nhan-xet.select-detai', compact('deTais'));
    }

    public function show($deTaiId)
    {
        $deTai = DeTai::findOrFail($deTaiId);
        $bienBan = BienBanNhanXet::where('ma_de_tai', $deTai->ma_de_tai)
            ->orderByDesc('created_at')
            ->with('cauTraLois')
            ->first();
        if (!$bienBan) {
            return redirect()->back()->with('error', 'Chưa có biên bản nhận xét cho đề tài này.');
        }
        return view('giangvien.bien-ban-nhan-xet.show', compact('bienBan', 'deTai'));
    }

    public function edit($deTaiId)
    {
        $deTai = DeTai::findOrFail($deTaiId);
        $bienBan = BienBanNhanXet::where('ma_de_tai', $deTai->ma_de_tai)
            ->orderByDesc('created_at')
            ->with('cauTraLois')
            ->first();
        if (!$bienBan) {
            return redirect()->back()->with('error', 'Chưa có biên bản nhận xét để sửa.');
        }
        return view('giangvien.bien-ban-nhan-xet.edit', compact('bienBan', 'deTai'));
    }

    public function update(Request $request, $deTaiId)
    {
        $request->validate([
            'hinh_thuc' => 'nullable',
            'cap_thiet' => 'nullable',
            'muc_tieu' => 'nullable',
            'tai_lieu' => 'nullable',
            'phuong_phap' => 'nullable',
            'ket_qua' => 'required|in:Đạt,Không đạt',
            'qua_trinh_hoat_dong' => 'nullable',
            'cau_hoi' => 'required|array',
        ]);
        $deTai = DeTai::findOrFail($deTaiId);
        $bienBan = BienBanNhanXet::where('ma_de_tai', $deTai->ma_de_tai)
            ->orderByDesc('created_at')
            ->firstOrFail();
        $bienBan->update([
            'hinh_thuc' => $request->hinh_thuc,
            'cap_thiet' => $request->cap_thiet,
            'muc_tieu' => $request->muc_tieu,
            'tai_lieu' => $request->tai_lieu,
            'phuong_phap' => $request->phuong_phap,
            'ket_qua' => $request->ket_qua,
            'qua_trinh_hoat_dong' => $request->qua_trinh_hoat_dong,
        ]);
        // Xóa các câu hỏi cũ
        $bienBan->cauTraLois()->delete();
        // Lưu lại các câu hỏi mới
        foreach ($request->cau_hoi as $cauHoi) {
            if (trim($cauHoi)) {
                $bienBan->cauTraLois()->create([
                    'cau_hoi' => $cauHoi,
                ]);
            }
        }
        return redirect()->route('giangvien.bien-ban-nhan-xet.select-detai')->with('success', 'Cập nhật biên bản thành công!');
    }
} 