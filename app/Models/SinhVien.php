<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SinhVien extends Model
{
    protected $fillable = ['mssv', 'ten'];

    public function chiTietNhom()
    {
        return $this->hasOne(ChiTietNhom::class);
    }

    public function bangDiems()
    {
        return $this->hasMany(BangDiem::class);
    }

    public function dangKyGiangVienHuongDan(): HasOne
    {
        return $this->hasOne(DangKyGiangVienHuongDan::class);
    }
}

