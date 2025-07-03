<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaoCaoQuaTrinh extends Model
{
    protected $table = 'bao_cao_qua_trinhs';

    protected $fillable = [
        'nhom_id',
        'dot_bao_cao_id',
        'noi_dung_bao_cao',
        'ngay_bao_cao',
    ];

    public function nhom()
    {
        return $this->belongsTo(Nhom::class, 'nhom_id');
    }

    public function dotBaoCao()
    {
        return $this->belongsTo(DotBaoCao::class, 'dot_bao_cao_id');
    }
}

