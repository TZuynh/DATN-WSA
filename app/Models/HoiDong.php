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

