<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class DotBaoCao extends Model
{
    use HasFactory;

    protected $fillable = [
        'nam_hoc',
        'ngay_bat_dau',
        'ngay_ket_thuc',
        'trang_thai',
        'mo_ta',
        'so_luong_hoi_dong',
        'so_luong_de_tai',
        'so_luong_nhom',
        'ti_do_hoan_thanh'
    ];

    protected $casts = [
        'ngay_bat_dau' => 'date',
        'ngay_ket_thuc' => 'date',
        'so_luong_hoi_dong' => 'integer',
        'so_luong_de_tai' => 'integer',
        'so_luong_nhom' => 'integer',
        'ti_do_hoan_thanh' => 'decimal:2'
    ];

    // Các trạng thái có thể có của đợt báo cáo
    const TRANG_THAI_CHUA_BAT_DAU = 'chua_bat_dau';
    const TRANG_THAI_DANG_DIEN_RA = 'dang_dien_ra';
    const TRANG_THAI_DA_KET_THUC = 'da_ket_thuc';
    const TRANG_THAI_DA_HUY = 'da_huy';

    // Các mối quan hệ
    public function chiTietBaoCaos()
    {
        return $this->hasMany(ChiTietDeTaiBaoCao::class);
    }

    public function bangDiems()
    {
        return $this->hasMany(BangDiem::class);
    }

    public function lichChams()
    {
        return $this->hasMany(LichCham::class);
    }

    public function hoiDongs()
    {
        return $this->hasMany(HoiDong::class);
    }

    public function deTais()
    {
        return $this->hasMany(DeTai::class);
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
        if ($this->trang_thai === self::TRANG_THAI_DA_HUY) {
            return;
        }

        $now = Carbon::now()->startOfDay(); // Lấy ngày hiện tại, bỏ qua giờ phút giây
        $ngayBatDau = Carbon::parse($this->ngay_bat_dau)->startOfDay();
        $ngayKetThuc = Carbon::parse($this->ngay_ket_thuc)->startOfDay();

        if ($now >= $ngayBatDau) {
            $this->trang_thai = self::TRANG_THAI_DANG_DIEN_RA;
        }
        
        if ($now > $ngayKetThuc) {
            $this->trang_thai = self::TRANG_THAI_DA_KET_THUC;
        }

        $this->save();
    }

    public function updateThongKe()
    {
        $this->so_luong_hoi_dong = $this->hoiDongs()->count();
        $this->so_luong_de_tai = $this->deTais()->count();
        $this->so_luong_nhom = $this->deTais()->whereNotNull('nhom_id')->count();
        
        // Tính tỷ lệ hoàn thành dựa trên số lượng báo cáo đã nộp
        $tongSoBaoCao = $this->chiTietBaoCaos()->count();
        $baoCaoDaNop = $this->chiTietBaoCaos()->whereNotNull('ngay_nop')->count();
        
        $this->ti_do_hoan_thanh = $tongSoBaoCao > 0 
            ? round(($baoCaoDaNop / $tongSoBaoCao) * 100, 2) 
            : 0;

        $this->save();
    }

    // Boot method để tự động cập nhật trạng thái khi tạo mới hoặc cập nhật
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($dotBaoCao) {
            if (!$dotBaoCao->trang_thai) {
                $dotBaoCao->trang_thai = self::TRANG_THAI_CHUA_BAT_DAU;
            }
        });

        static::saved(function ($dotBaoCao) {
            $dotBaoCao->updateTrangThai();
            // Cập nhật trạng thái cho tất cả đề tài thuộc đợt báo cáo này
            $dotBaoCao->deTais()->get()->each->updateTrangThai();
        });
    }
}

