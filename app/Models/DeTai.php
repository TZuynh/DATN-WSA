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
    const TRANG_THAI_DANG_CHO_DUYET = 0;
    const TRANG_THAI_DANG_THUC_HIEN_GVHD = 1;
    const TRANG_THAI_DANG_THUC_HIEN_GVPB = 2;
    const TRANG_THAI_KHONG_XAY_RA_GVHD = 3;
    const TRANG_THAI_KHONG_XAY_RA_GVPB = 4;

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

    // Phương thức tạo mã đề tài tự động
    public static function generateMaDeTai()
    {
        do {
            $randomNumber = str_pad(mt_rand(0, 99999), 5, '0', STR_PAD_LEFT);
            $maDeTai = 'MDT-' . $randomNumber;
        } while (self::where('ma_de_tai', $maDeTai)->exists());

        return $maDeTai;
    }

    // Các phương thức hỗ trợ
    public function getTrangThaiTextAttribute()
    {
        return match($this->trang_thai) {
            self::TRANG_THAI_DANG_CHO_DUYET => 'Đang chờ duyệt',
            self::TRANG_THAI_DANG_THUC_HIEN_GVHD => 'Đang thực hiện (giảng viên hướng dẫn đồng ý báo cáo)',
            self::TRANG_THAI_DANG_THUC_HIEN_GVPB => 'Đang thực hiện (giáo viên phản biện đồng ý báo cáo)',
            self::TRANG_THAI_KHONG_XAY_RA_GVHD => 'Không xảy ra (giảng viên hướng dẫn không đồng ý)',
            self::TRANG_THAI_KHONG_XAY_RA_GVPB => 'Không xảy ra (giảng viên phản biện không đồng ý)',
            default => 'Không xác định'
        };
    }

    public function getTrangThaiClassAttribute()
    {
        return match($this->trang_thai) {
            self::TRANG_THAI_DANG_CHO_DUYET => 'badge bg-warning',
            self::TRANG_THAI_DANG_THUC_HIEN_GVHD => 'badge bg-info',
            self::TRANG_THAI_DANG_THUC_HIEN_GVPB => 'badge bg-success',
            self::TRANG_THAI_KHONG_XAY_RA_GVHD => 'badge bg-danger',
            self::TRANG_THAI_KHONG_XAY_RA_GVPB => 'badge bg-danger',
            default => 'badge bg-secondary'
        };
    }

    public function updateTrangThai()
    {
        if (!$this->dotBaoCao) {
            return;
        }

        if ($this->trang_thai === self::TRANG_THAI_KHONG_XAY_RA_GVHD || 
            $this->trang_thai === self::TRANG_THAI_KHONG_XAY_RA_GVPB) {
            return;
        }

        $now = Carbon::now();
        
        // Lấy ngày từ đợt báo cáo
        $ngayBatDau = $this->dotBaoCao->ngay_bat_dau;
        $ngayKetThuc = $this->dotBaoCao->ngay_ket_thuc;

        if ($now >= $ngayBatDau) {
            $this->trang_thai = self::TRANG_THAI_DANG_THUC_HIEN_GVHD;
        }
        
        if ($now > $ngayKetThuc) {
            $this->trang_thai = self::TRANG_THAI_DANG_THUC_HIEN_GVPB;
        }

        $this->save();
    }

    // Boot method để tự động cập nhật trạng thái khi tạo mới hoặc cập nhật
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($deTai) {
            if (!isset($deTai->trang_thai)) {
                $deTai->trang_thai = self::TRANG_THAI_DANG_CHO_DUYET;
            }
            if (!isset($deTai->ma_de_tai)) {
                $deTai->ma_de_tai = self::generateMaDeTai();
            }
        });

        static::saved(function ($deTai) {
            $deTai->updateTrangThai();
        });
    }
}

