<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Nhom extends Model
{
    protected $table = 'nhoms';

    protected $fillable = [
        'ma_nhom',
        'ten',
        'giang_vien_id',
        'trang_thai',
        'de_tai_id'
    ];

    /**
     * Lấy giảng viên hướng dẫn của nhóm
     */
    public function giangVien(): BelongsTo
    {
        return $this->belongsTo(TaiKhoan::class, 'giang_vien_id');
    }

    /**
     * Lấy danh sách sinh viên trong nhóm
     */
    public function sinhViens(): BelongsToMany
    {
        return $this->belongsToMany(SinhVien::class, 'chi_tiet_nhoms', 'nhom_id', 'sinh_vien_id');
    }

    /**
     * Lấy đề tài của nhóm
     */
    public function deTai()
    {
        return $this->hasOne(\App\Models\DeTai::class, 'nhom_id', 'id');
    }

    /**
     * Lấy chi tiết nhóm
     */
    public function chiTietNhoms(): HasMany
    {
        return $this->hasMany(ChiTietNhom::class);
    }

    /**
     * Lấy danh sách lịch chấm của nhóm
     */
    public function lichChams(): HasMany
    {
        return $this->hasMany(LichCham::class);
    }

    /**
     * Tạo mã nhóm tự động
     */
    public static function taoMaNhom()
    {
        // Lấy năm hiện tại
        $namHienTai = date('Y');
        
        // Đếm số nhóm trong năm hiện tại
        $soNhom = self::where('ma_nhom', 'like', "NH{$namHienTai}%")->count();
        
        // Tạo mã nhóm theo format: NHYYYY-XXX (XXX là số thứ tự)
        $maNhom = 'NH' . $namHienTai . '-' . str_pad($soNhom + 1, 3, '0', STR_PAD_LEFT);
        
        return $maNhom;
    }
}
