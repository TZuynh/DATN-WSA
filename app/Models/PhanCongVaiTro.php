<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhanCongVaiTro extends Model
{
    protected $table = 'phan_cong_vai_tros';

    protected $fillable = [
        'hoi_dong_id',
        'tai_khoan_id',
        'vai_tro_id'
    ];

    public function hoiDong()
    {
        return $this->belongsTo(HoiDong::class);
    }

    public function vaiTro()
    {
        return $this->belongsTo(VaiTro::class);
    }

    public function taiKhoan()
    {
        return $this->belongsTo(TaiKhoan::class);
    }
}

