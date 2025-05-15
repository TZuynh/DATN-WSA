<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DangKyDeTai extends Model
{
    protected $table = 'dang_ky_de_tai';

    protected $fillable = [
        'sinh_vien_id',
        'de_tai_id',
        'trang_thai',
    ];

    public function sinhVien()
    {
        return $this->belongsTo(SinhVien::class, 'sinh_vien_id');
    }

    public function deTai()
    {
        return $this->belongsTo(DeTai::class, 'de_tai_id');
    }
}

