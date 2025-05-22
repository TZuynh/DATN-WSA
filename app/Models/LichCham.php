<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LichCham extends Model
{
    protected $fillable = [
        'nhom_id', 'hoi_dong_id', 'dot_bao_cao_id', 'lich_tao',
    ];

    public function nhom()
    {
        return $this->belongsTo(Nhom::class);
    }

    public function hoiDong()
    {
        return $this->belongsTo(HoiDong::class);
    }

    public function dotBaoCao()
    {
        return $this->belongsTo(DotBaoCao::class);
    }
}

