<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BienBanNhanXet extends Model
{
    use HasFactory;

    protected $table = 'bien_bang_nhan_xets';

    protected $fillable = [
        'hoi_dong_id',
        'dot_bao_cao_id',
        'ma_de_tai',
        'noi_dung_bien_ban',
        'ngay_lap',
        'nguoi_lap',
        'hinh_thuc',
        'cap_thiet',
        'muc_tieu',
        'tai_lieu',
        'phuong_phap',
        'ket_qua',
    ];

    public function hoiDong()
    {
        return $this->belongsTo(HoiDong::class, 'hoi_dong_id');
    }

    public function dotBaoCao()
    {
        return $this->belongsTo(DotBaoCao::class, 'dot_bao_cao_id');
    }

    public function cauTraLois()
    {
        return $this->hasMany(\App\Models\BienBanCauTraLoi::class, 'bien_ban_nhan_xet_id');
    }
} 