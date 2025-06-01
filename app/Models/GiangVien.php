<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GiangVien extends Model
{
    use HasFactory;

    protected $table = 'giang_viens';

    protected $fillable = [
        'ten',
        'email',
        'so_dien_thoai'
    ];

    public function dangKyGiangVienHuongDans()
    {
        return $this->hasMany(DangKyGiangVienHuongDan::class);
    }

    public function nhoms()
    {
        return $this->hasMany(Nhom::class);
    }
} 