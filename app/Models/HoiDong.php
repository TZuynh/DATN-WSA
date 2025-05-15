<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HoiDong extends Model
{
    protected $fillable = ['ten'];

    public function thanhViens()
    {
        return $this->hasMany(ThanhVienHoiDong::class);
    }

    public function chiTietBaoCaos()
    {
        return $this->hasMany(ChiTietDeTaiBaoCao::class);
    }

    public function lichChams()
    {
        return $this->hasMany(LichCham::class);
    }
}

