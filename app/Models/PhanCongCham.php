<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhanCongCham extends Model
{
    protected $table = 'phan_cong_chams';

    protected $fillable = [
        'de_tai_id',
        'giang_vien_huong_dan_id',
        'giang_vien_phan_bien_id',
        'giang_vien_khac_id',
        'ngay_phan_cong'
    ];

    protected $casts = [
        'ngay_phan_cong' => 'date'
    ];

    public function deTai()
    {
        return $this->belongsTo(DeTai::class, 'de_tai_id');
    }

    public function giangVienHuongDan()
    {
        return $this->belongsTo(TaiKhoan::class, 'giang_vien_huong_dan_id');
    }

    public function giangVienPhanBien()
    {
        return $this->belongsTo(TaiKhoan::class, 'giang_vien_phan_bien_id');
    }

    public function giangVienKhac()
    {
        return $this->belongsTo(TaiKhoan::class, 'giang_vien_khac_id');
    }
} 