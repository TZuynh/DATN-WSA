<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeTai extends Model
{
    protected $fillable = [
        'ma_de_tai', 'tieu_de', 'mo_ta', 'ngay_bat_dau', 'ngay_ket_thuc',
        'nhom_id', 'giang_vien_id',
    ];

    public function nhom()
    {
        return $this->belongsTo(Nhom::class);
    }

    public function giangVien()
    {
        return $this->belongsTo(TaiKhoan::class, 'giang_vien_id');
    }

    public function chiTietBaoCaos()
    {
        return $this->hasMany(ChiTietDeTaiBaoCao::class, 'de_tai_id');
    }
}

