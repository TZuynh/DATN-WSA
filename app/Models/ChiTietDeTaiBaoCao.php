<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChiTietDeTaiBaoCao extends Model
{
    protected $fillable = ['dot_bao_cao_id', 'de_tai_id', 'hoi_dong_id'];

    public function deTai()
    {
        return $this->belongsTo(DeTai::class);
    }

    public function hoiDong()
    {
        return $this->belongsTo(\App\Models\HoiDong::class, 'hoi_dong_id');
    }

    public function dotBaoCao()
    {
        return $this->belongsTo(DotBaoCao::class);
    }
}

