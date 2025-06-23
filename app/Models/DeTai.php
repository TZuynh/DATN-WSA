<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DeTai extends Model
{
    protected $fillable = [
        'ma_de_tai', 'ten_de_tai', 'mo_ta', 'ngay_bat_dau', 'ngay_ket_thuc',
        'giang_vien_id', 'dot_bao_cao_id', 'trang_thai', 'y_kien_giang_vien'
    ];

    // Các trạng thái của đề tài
    const TRANG_THAI_CHO_DUYET = 0;
    const TRANG_THAI_DANG_THUC_HIEN_GVHD = 1;
    const TRANG_THAI_DANG_THUC_HIEN_GVPB = 2;
    const TRANG_THAI_KHONG_XAY_RA_GVHD = 3;
    const TRANG_THAI_KHONG_XAY_RA_GVPB = 4;

    // Mảng ánh xạ trạng thái sang text
    const TRANG_THAI_TEXT = [
        self::TRANG_THAI_CHO_DUYET => 'Chờ duyệt',
        self::TRANG_THAI_DANG_THUC_HIEN_GVHD => 'Đang thực hiện (GVHD đồng ý)',
        self::TRANG_THAI_DANG_THUC_HIEN_GVPB => 'Đang thực hiện (GVPB đồng ý)',
        self::TRANG_THAI_KHONG_XAY_RA_GVHD => 'Không xảy ra (GVHD không đồng ý)',
        self::TRANG_THAI_KHONG_XAY_RA_GVPB => 'Không xảy ra (GVPB không đồng ý)'
    ];

    // Mảng ánh xạ trạng thái sang class CSS
    const TRANG_THAI_CLASS = [
        self::TRANG_THAI_CHO_DUYET => 'bg-warning',
        self::TRANG_THAI_DANG_THUC_HIEN_GVHD => 'bg-info',
        self::TRANG_THAI_DANG_THUC_HIEN_GVPB => 'bg-primary',
        self::TRANG_THAI_KHONG_XAY_RA_GVHD => 'bg-danger',
        self::TRANG_THAI_KHONG_XAY_RA_GVPB => 'bg-danger'
    ];

    protected $casts = [
        'ngay_bat_dau' => 'date',
        'ngay_ket_thuc' => 'date',
        'trang_thai' => 'integer'
    ];

    public function nhoms()
    {
        return $this->hasMany(Nhom::class, 'de_tai_id');
    }

    public function nhom()
    {
        return $this->belongsTo(Nhom::class, 'nhom_id');
    }

    public function giangVien()
    {
        return $this->belongsTo(TaiKhoan::class, 'giang_vien_id');
    }

    public function chiTietBaoCao()
    {
        return $this->hasOne(\App\Models\ChiTietDeTaiBaoCao::class, 'de_tai_id');
    }

    public function dotBaoCao()
    {
        return $this->belongsTo(DotBaoCao::class);
    }

    public function phanCongCham()
    {
        return $this->hasOne(\App\Models\PhanCongCham::class, 'de_tai_id', 'id');
    }

    public function lichCham()
    {
        return $this->hasOne(LichCham::class);
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
        switch ($this->trang_thai) {
            case 2:
            case '2':
                return 'Giảng viên phản biện đã duyệt';
            case 4:
            case '4':
                return 'Giảng viên phản biện từ chối';
            case 0:
            case '0':
                return 'Đang chờ duyệt';
            case 1:
            case '1':
                return 'Đang thực hiện (giảng viên hướng dẫn đồng ý báo cáo)';
            case 3:
            case '3':
                return 'Không xảy ra (giảng viên hướng dẫn không đồng ý)';
            default:
                return 'Không xác định';
        }
    }

    public function getTrangThaiClassAttribute()
    {
        switch ($this->trang_thai) {
            case 2:
            case '2':
                return 'bg-success';
            case 4:
            case '4':
                return 'bg-danger';
            case 0:
            case '0':
                return 'bg-warning';
            case 1:
            case '1':
                return 'bg-primary';
            case 3:
            case '3':
                return 'bg-secondary';
            default:
                return 'bg-secondary';
        }
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
                $deTai->trang_thai = self::TRANG_THAI_CHO_DUYET;
            }
            if (!isset($deTai->ma_de_tai)) {
                $deTai->ma_de_tai = self::generateMaDeTai();
            }
        });

        // static::saved(function ($deTai) {
        //     $deTai->updateTrangThai();
        // });
    }
}

