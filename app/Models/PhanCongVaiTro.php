<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhanCongVaiTro extends Model
{
    protected $table = 'phan_cong_vai_tro';

    protected $fillable = [
        'hoi_dong_id',
        'tai_khoan_id',
        'role_id'
    ];

    public function hoiDong()
    {
        return $this->belongsTo(HoiDong::class, 'hoi_dong_id');
    }

    public function taiKhoan()
    {
        return $this->belongsTo(TaiKhoan::class, 'tai_khoan_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}

