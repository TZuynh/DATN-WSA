<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThanhVienHoiDong extends Model
{
    protected $fillable = ['hoi_dong_id', 'tai_khoan_id', 'vai_tro'];

    public function taiKhoan()
    {
        return $this->belongsTo(TaiKhoan::class);
    }

    public function hoiDong()
    {
        return $this->belongsTo(HoiDong::class);
    }
}

