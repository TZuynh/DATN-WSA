<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DeTai extends Model
{
    protected $fillable = [
        'ma_de_tai', 'de_tai_mau_id', 'mo_ta', 'ngay_bat_dau', 'ngay_ket_thuc',
        'nhom_id', 'giang_vien_id', 'dot_bao_cao_id', 'trang_thai'
    ];

    // Các trạng thái có thể có của đề tài
    const TRANG_THAI_CHUA_BAT_DAU = 'chua_bat_dau';
    const TRANG_THAI_DANG_DIEN_RA = 'dang_dien_ra';
    const TRANG_THAI_DA_KET_THUC = 'da_ket_thuc';
    const TRANG_THAI_DA_HUY = 'da_huy';

    protected $casts = [
        'ngay_bat_dau' => 'date',
        'ngay_ket_thuc' => 'date'
    ];

    public function nhom()
    {
        return $this->belongsTo(Nhom::class);
    }

    public function giangVien()
    {
        return $this->belongsTo(TaiKhoan::class, 'giang_vien_id');
    }

    public function chiTietBaoCaos()
    {
        return $this->hasMany(ChiTietDeTaiBaoCao::class, 'de_tai_id');
    }

    public function dotBaoCao()
    {
        return $this->belongsTo(DotBaoCao::class);
    }

    public function dotDeTai()
    {
        return $this->hasMany(DotDeTai::class);
    }

    public function deTaiMau()
    {
        return $this->belongsTo(DeTaiMau::class);
    }

    // Các phương thức hỗ trợ
    public function getTrangThaiTextAttribute()
    {
        return match($this->trang_thai) {
            self::TRANG_THAI_CHUA_BAT_DAU => 'Chưa bắt đầu',
            self::TRANG_THAI_DANG_DIEN_RA => 'Đang diễn ra',
            self::TRANG_THAI_DA_KET_THUC => 'Đã kết thúc',
            self::TRANG_THAI_DA_HUY => 'Đã hủy',
            default => 'Không xác định'
        };
    }

    public function getTrangThaiClassAttribute()
    {
        return match($this->trang_thai) {
            self::TRANG_THAI_CHUA_BAT_DAU => 'bg-gray-100 text-gray-800',
            self::TRANG_THAI_DANG_DIEN_RA => 'bg-blue-100 text-blue-800',
            self::TRANG_THAI_DA_KET_THUC => 'bg-green-100 text-green-800',
            self::TRANG_THAI_DA_HUY => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function updateTrangThai()
    {
        if (!$this->dotBaoCao) {
            return;
        }

        if ($this->trang_thai === self::TRANG_THAI_DA_HUY) {
            return;
        }

        $now = Carbon::now();
        
        // Lấy ngày từ đợt báo cáo
        $ngayBatDau = $this->dotBaoCao->ngay_bat_dau;
        $ngayKetThuc = $this->dotBaoCao->ngay_ket_thuc;

        if ($now >= $ngayBatDau) {
            $this->trang_thai = self::TRANG_THAI_DANG_DIEN_RA;
        }
        
        if ($now > $ngayKetThuc) {
            $this->trang_thai = self::TRANG_THAI_DA_KET_THUC;
        }

        $this->save();
    }

    // Boot method để tự động cập nhật trạng thái khi tạo mới hoặc cập nhật
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($deTai) {
            if (!$deTai->trang_thai) {
                $deTai->trang_thai = self::TRANG_THAI_CHUA_BAT_DAU;
            }
        });

        static::saved(function ($deTai) {
            $deTai->updateTrangThai();
        });
    }
}

