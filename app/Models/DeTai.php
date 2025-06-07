<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DeTai extends Model
{
    protected $fillable = [
        'ma_de_tai', 'ten_de_tai', 'mo_ta', 'ngay_bat_dau', 'ngay_ket_thuc',
        'nhom_id', 'giang_vien_id', 'dot_bao_cao_id', 'trang_thai', 'y_kien_giang_vien'
    ];

    // Các trạng thái có thể có của đề tài
    const TRANG_THAI_CHUA_BAT_DAU = 0;
    const TRANG_THAI_DANG_DIEN_RA = 1;
    const TRANG_THAI_DA_KET_THUC = 2;
    const TRANG_THAI_DA_HUY = 3;
    const TRANG_THAI_DANG_CHO_DUYET = 4;

    protected $casts = [
        'ngay_bat_dau' => 'date',
        'ngay_ket_thuc' => 'date',
        'trang_thai' => 'integer'
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


    // Các phương thức hỗ trợ
    public function getTrangThaiTextAttribute()
    {
        return match($this->trang_thai) {
            self::TRANG_THAI_CHUA_BAT_DAU => 'Chưa bắt đầu',
            self::TRANG_THAI_DANG_DIEN_RA => 'Đang diễn ra',
            self::TRANG_THAI_DA_KET_THUC => 'Đã kết thúc',
            self::TRANG_THAI_DA_HUY => 'Đã hủy',
            self::TRANG_THAI_DANG_CHO_DUYET => 'Đang chờ duyệt',
            default => 'Không xác định'
        };
    }

    public function getTrangThaiClassAttribute()
    {
        return match($this->trang_thai) {
            self::TRANG_THAI_CHUA_BAT_DAU => 'badge bg-warning',
            self::TRANG_THAI_DANG_DIEN_RA => 'badge bg-info',
            self::TRANG_THAI_DA_KET_THUC => 'badge bg-success',
            self::TRANG_THAI_DA_HUY => 'badge bg-danger',
            self::TRANG_THAI_DANG_CHO_DUYET => 'badge bg-warning',
            default => 'badge bg-secondary'
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
            if (!isset($deTai->trang_thai)) {
                $deTai->trang_thai = self::TRANG_THAI_CHUA_BAT_DAU;
            }
        });

        static::saved(function ($deTai) {
            $deTai->updateTrangThai();
        });
    }
}

