<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NhanXet extends Model
{
    protected $table = 'nhan_xet';

    protected $fillable = [
        'bang_diem_id',
        'tai_khoan_id',
        'noi_dung_nhan_xet',
    ];

    public function bangDiem()
    {
        return $this->belongsTo(BangDiem::class, 'bang_diem_id');
    }

    public function giaoVien()
    {
        return $this->belongsTo(TaiKhoan::class, 'tai_khoan_id');
    }
}

