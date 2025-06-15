<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HoiDong extends Model
{
    use HasFactory;

    protected $table = 'hoi_dongs';

    protected $fillable = [
        'ma_hoi_dong',
        'ten',
        'dot_bao_cao_id',
        'de_tai_id'
    ];

    public function dotBaoCao()
    {
        return $this->belongsTo(DotBaoCao::class);
    }

    public function deTai()
    {
        return $this->belongsTo(DeTai::class);
    }

    public function chiTietBaoCaos()
    {
        return $this->hasMany(ChiTietDeTaiBaoCao::class);
    }

    public function lichChams()
    {
        return $this->hasMany(LichCham::class);
    }

    public function phanCongVaiTros()
    {
        return $this->hasMany(PhanCongVaiTro::class);
    }

    /**
     * Tạo mã hội đồng tự động
     */
    public static function taoMaHoiDong($dotBaoCaoId)
    {
        $dotBaoCao = DotBaoCao::find($dotBaoCaoId);
        if (!$dotBaoCao) {
            throw new \Exception('Không tìm thấy đợt báo cáo');
        }

        // Lấy năm học từ đợt báo cáo (ví dụ: 2023-2024)
        $namHoc = explode('-', $dotBaoCao->nam_hoc);
        $namBatDau = $namHoc[0];
        
        // Đếm số hội đồng trong đợt báo cáo này
        $soHoiDong = self::where('dot_bao_cao_id', $dotBaoCaoId)->count();
        
        // Tạo mã hội đồng theo format: HD-YYYY-XXX (XXX là số thứ tự)
        $maHoiDong = 'HD-' . $namBatDau . '-' . str_pad($soHoiDong + 1, 3, '0', STR_PAD_LEFT);
        
        return $maHoiDong;
    }

    /**
     * Thêm tất cả đề tài của giảng viên vào hội đồng
     */
    public function themDeTaiCuaGiangVien($giangVienId)
    {
        // Lấy tất cả đề tài của giảng viên
        $deTais = DeTai::where('giang_vien_id', $giangVienId)
            ->where('trang_thai', '!=', DeTai::TRANG_THAI_KHONG_XAY_RA_GVHD)
            ->where('trang_thai', '!=', DeTai::TRANG_THAI_KHONG_XAY_RA_GVPB)
            ->get();

        // Lấy đợt báo cáo hiện tại
        $dotBaoCao = DotBaoCao::where('trang_thai', 'dang_dien_ra')->first();

        if (!$dotBaoCao) {
            throw new \Exception('Không tìm thấy đợt báo cáo đang diễn ra');
        }

        // Thêm từng đề tài vào hội đồng
        foreach ($deTais as $deTai) {
            ChiTietDeTaiBaoCao::create([
                'hoi_dong_id' => $this->id,
                'de_tai_id' => $deTai->id,
                'dot_bao_cao_id' => $dotBaoCao->id
            ]);
        }
    }
}

