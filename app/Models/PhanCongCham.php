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
        if (!$this->hoiDong) {
            return null;
        }

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

    public function getVaiTroChamFor($giangVienId)
    {
        if ((int)$this->giang_vien_phan_bien_id === (int)$giangVienId) {
            return 'Phản biện';
        }
        if ((int)$this->giang_vien_khac_id === (int)$giangVienId) {
            return 'Giảng viên khác';
        }
        if ((int)$this->giang_vien_huong_dan_id === (int)$giangVienId) {
            return 'Hướng dẫn';
        }
        return 'N/A';
    }
}
