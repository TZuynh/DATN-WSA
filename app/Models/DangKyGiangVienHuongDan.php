<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DangKyGiangVienHuongDan extends Model
{
    protected $table = 'dang_ky_giang_vien_huong_dans';

    protected $fillable = [
        'sinh_vien_id',
        'giang_vien_id',
        'trang_thai',
    ];

    public function sinhVien(): BelongsTo
    {
        return $this->belongsTo(SinhVien::class);
    }

    public function giangVien(): BelongsTo
    {
        return $this->belongsTo(TaiKhoan::class, 'giang_vien_id');
    }
}
