<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DangKyGiaoVienHuongDan extends Model
{
    protected $table = 'dang_ky_giao_vien_huong_dan';

    protected $fillable = [
        'sinh_vien_id',
        'giang_vien_id',
        'trang_thai',
    ];

    public function sinhVien()
    {
        return $this->belongsTo(SinhVien::class, 'sinh_vien_id');
    }

    public function giaoVien()
    {
        return $this->belongsTo(TaiKhoan::class, 'giang_vien_id');
    }
}
