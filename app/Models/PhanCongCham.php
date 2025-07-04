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
        if (!$this->hoiDong) return null;
        $phanCong = $this->hoiDong->phanCongVaiTros()
            ->where('loai_giang_vien', $loai)
            ->first();
        return $phanCong ? $phanCong->taiKhoan : null;
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

    public function giangVienPhanBien()
    {
        return $this->hasOneThrough(
            \App\Models\TaiKhoan::class,
            \App\Models\PhanCongVaiTro::class,
            'hoi_dong_id',
            'id',
            'hoi_dong_id',
            'tai_khoan_id'
        )->where('loai_giang_vien', 'Giảng Viên Phản Biện');
    }
}
