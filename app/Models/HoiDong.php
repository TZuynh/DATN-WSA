<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HoiDong extends Model
{
    protected $fillable = ['ma_hoi_dong', 'ten', 'dot_bao_cao_id'];

    public function thanhViens()
    {
        return $this->hasMany(ThanhVienHoiDong::class);
    }

    public function dotBaoCao()
    {
        return $this->belongsTo(DotBaoCao::class);
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

