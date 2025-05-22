<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BienBanNhanXet extends Model
{
    use HasFactory;

    protected $table = 'bien_ban_nhan_xets';

    protected $fillable = [
        'hoi_dong_id',
        'dot_bao_cao_id',
        'noi_dung',
        'ngay_lap',
        'nguoi_lap'
    ];

    public function hoiDong()
    {
        return $this->belongsTo(HoiDong::class, 'hoi_dong_id');
    }

    public function dotBaoCao()
    {
        return $this->belongsTo(DotBaoCao::class, 'dot_bao_cao_id');
    }
} 