<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class TaiKhoan extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'ten', 'email', 'mat_khau', 'vai_tro',
    ];

    protected $hidden = ['mat_khau'];

    public function nhoms()
    {
        return $this->hasMany(Nhom::class, 'giang_vien_id');
    }

    public function thanhVienHoiDong()
    {
        return $this->hasMany(ThanhVienHoiDong::class, 'tai_khoan_id');
    }

    public function bangDiems()
    {
        return $this->hasMany(BangDiem::class, 'giang_vien_id');
    }
}

