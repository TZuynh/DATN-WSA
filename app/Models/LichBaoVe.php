<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LichBaoVe extends Model
{
    protected $table = 'lich_bao_ve';

    protected $fillable = [
        'nhom_id',
        'hoi_dong_id',
        'ngay_gio_bao_ve',
        'dia_diem',
    ];

    public function nhom()
    {
        return $this->belongsTo(Nhom::class, 'nhom_id');
    }

    public function hoiDong()
    {
        return $this->belongsTo(HoiDong::class, 'hoi_dong_id');
    }
}

