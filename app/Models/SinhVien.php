<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SinhVien extends Model
{
    use HasFactory;

    protected $table = 'sinh_viens';

    protected $fillable = [
        'mssv',
        'ten',
        'lop',
        'nganh',
        'khoa_hoc'
    ];

    public function nhom()
    {
        return $this->hasOne(Nhom::class);
    }

    public function nhoms()
    {
        return $this->belongsToMany(Nhom::class, 'chi_tiet_nhoms', 'sinh_vien_id', 'nhom_id');
    }

    public function dangKys()
    {
        return $this->hasMany(DangKyGiangVienHuongDan::class);
    }

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

