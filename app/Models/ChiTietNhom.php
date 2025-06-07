<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChiTietNhom extends Model
{
    protected $fillable = ['nhom_id', 'sinh_vien_id', 'diem_tong_ket'];

    protected $casts = [
        'diem_tong_ket' => 'decimal:2'
    ];

    public function nhom()
    {
        return $this->belongsTo(Nhom::class);
    }

    public function sinhVien()
    {
        return $this->belongsTo(SinhVien::class);
    }
}

