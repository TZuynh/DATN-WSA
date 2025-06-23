<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhanCongCham extends Model
{
    protected $table = 'phan_cong_chams';

    protected $fillable = [
        'de_tai_id',
        'hoi_dong_id',
        'lich_cham'
    ];

    protected $casts = [
        'lich_cham' => 'datetime'
    ];

    public function deTai()
    {
        return $this->belongsTo(DeTai::class, 'de_tai_id');
    }

    public function hoiDong()
    {
        return $this->belongsTo(HoiDong::class, 'hoi_dong_id');
    }

    public function getGiangVienByLoai($loai)
    {
        if (!$this->relationLoaded('hoiDong') || !$this->hoiDong->relationLoaded('phanCongVaiTros')) {
            return null;
        }

        $phanCong = $this->hoiDong->phanCongVaiTros
            ->firstWhere('loai_giang_vien', $loai);

        return $phanCong?->taiKhoan;
    }

    public function getGiangVienHuongDanAttribute()
    {
        return $this->getGiangVienByLoai('Giảng Viên Hướng Dẫn');
    }

    public function getGiangVienPhanBienAttribute()
    {
        return $this->getGiangVienByLoai('Giảng Viên Phản Biện');
    }

    public function getGiangVienKhacAttribute()
    {
        return $this->getGiangVienByLoai('Giảng Viên Khác');
    }
}
