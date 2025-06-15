<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nhom extends Model
{
    protected $fillable = ['ma_nhom', 'ten', 'giang_vien_id', 'de_tai_id', 'trang_thai'];

    public function chiTietNhoms()
    {
        return $this->hasMany(ChiTietNhom::class);
    }

    public function sinhViens()
    {
        return $this->belongsToMany(SinhVien::class, 'chi_tiet_nhoms', 'nhom_id', 'sinh_vien_id');
    }

    public function deTais()
    {
        return $this->hasMany(DeTai::class, 'nhom_id');
    }

    public function giangVien()
    {
        return $this->belongsTo(TaiKhoan::class, 'giang_vien_id');
    }

    public function lichChams()
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
