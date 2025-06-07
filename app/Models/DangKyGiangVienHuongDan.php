<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DangKyGiangVienHuongDan extends Model
{
    use HasFactory;

    protected $table = 'dang_ky_giang_vien_huong_dan';

    protected $fillable = [
        'giang_vien_id',
        'nhom_id',
        'trang_thai',
        'ghi_chu'
    ];

    public function giangVien()
    {
        return $this->belongsTo(TaiKhoan::class, 'giang_vien_id');
    }

    public function nhom()
    {
        return $this->belongsTo(Nhom::class, 'nhom_id');
    }
} 