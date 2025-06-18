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
        'lich_cham'
    ];

    protected $casts = [
        'lich_cham' => 'datetime'
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