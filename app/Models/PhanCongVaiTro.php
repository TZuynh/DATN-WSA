<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PhanCongVaiTro extends Model
{
    use HasFactory;

    protected $table = 'phan_cong_vai_tros';

    protected $fillable = [
        'hoi_dong_id',
        'tai_khoan_id',
        'vai_tro_id',
        'loai_giang_vien',
        'de_tai_id'
    ];

    public function deTai()
    {
        return $this->belongsTo(DeTai::class, 'de_tai_id');
    }
    
    public function hoiDong()
    {
        return $this->belongsTo(HoiDong::class, 'hoi_dong_id');
    }

    public function vaiTro()
    {
        return $this->belongsTo(VaiTro::class);
    }

    public function taiKhoan()
    {
        return $this->belongsTo(\App\Models\TaiKhoan::class, 'tai_khoan_id');
    }

    /**
     * Get the giang vien associated with the PhanCongVaiTro.
     */
    public function giangVien()
    {
        return $this->belongsTo(TaiKhoan::class, 'giang_vien_id');
    }
}

