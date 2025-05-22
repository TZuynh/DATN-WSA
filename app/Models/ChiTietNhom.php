<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChiTietNhom extends Model
{
    protected $fillable = ['nhom_id', 'sinh_vien_id'];

    public function nhom()
    {
        return $this->belongsTo(Nhom::class);
    }

    public function sinhVien()
    {
        return $this->belongsTo(SinhVien::class);
    }
}

