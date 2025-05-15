<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nhom extends Model
{
    protected $fillable = ['ma_nhom', 'ten', 'giang_vien_id', 'trang_thai'];

    public function chiTietNhoms()
    {
        return $this->hasMany(ChiTietNhom::class);
    }

    public function deTai()
    {
        return $this->hasOne(DeTai::class);
    }

    public function giangVien()
    {
        return $this->belongsTo(TaiKhoan::class, 'giang_vien_id');
    }

    public function lichChams()
    {
        return $this->hasMany(LichCham::class);
    }
}
