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

    public function hoiDong()
    {
        return $this->hasOneThrough(
            HoiDong::class,
            LichCham::class,
            'dot_bao_cao_id', // Foreign key trên lich_chams
            'id', // Foreign key trên hoi_dongs
            'id', // Local key trên dot_bao_caos
            'hoi_dong_id' // Local key trên lich_chams
        )->distinct();
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

        $now = Carbon::now()->startOfDay();
        $ngayBatDau = Carbon::parse($this->ngay_bat_dau)->startOfDay();
        $ngayKetThuc = Carbon::parse($this->ngay_ket_thuc)->startOfDay();

        $trangThaiMoi = match(true) {
            $now < $ngayBatDau => self::TRANG_THAI_CHUA_BAT_DAU,
            $now > $ngayKetThuc => self::TRANG_THAI_DA_KET_THUC,
            default => self::TRANG_THAI_DANG_DIEN_RA
        };

        if ($this->trang_thai !== $trangThaiMoi) {
            $this->trang_thai = $trangThaiMoi;
            $this->save();
        }
    }

    public function updateThongKe()
    {
        // Chỉ cập nhật thống kê nếu cần thiết
        $this->so_luong_hoi_dong = $this->hoiDongs()->count();
        $this->so_luong_de_tai = $this->deTais()->count();
        $this->so_luong_nhom = $this->deTais()->whereNotNull('nhom_id')->count();
        
        $tongSoBaoCao = $this->chiTietBaoCaos()->count();
        if ($tongSoBaoCao > 0) {
            $baoCaoDaNop = $this->chiTietBaoCaos()->whereNotNull('ngay_nop')->count();
            $this->ti_do_hoan_thanh = round(($baoCaoDaNop / $tongSoBaoCao) * 100, 2);
        } else {
            $this->ti_do_hoan_thanh = 0;
        }

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

    // Các phương thức lấy thông tin chi tiết
    public function getThongTinChiTiet()
    {
        // Sử dụng eager loading để tối ưu query
        return $this->load([
            'hoiDongs' => function($query) {
                $query->with([
                    'phanCongVaiTros' => function($query) {
                        $query->with('giangVien');
                    },
                    'chiTietBaoCaos' => function($query) {
                        $query->with(['deTai' => function($query) {
                            $query->with(['nhom', 'giangVien']);
                        }]);
                    }
                ]);
            }
        ]);
    }

    public function getThongKeChiTiet()
    {
        return [
            'tong_so_hoi_dong' => $this->so_luong_hoi_dong,
            'tong_so_de_tai' => $this->so_luong_de_tai,
            'tong_so_nhom' => $this->so_luong_nhom,
            'ti_do_hoan_thanh' => $this->ti_do_hoan_thanh,
            'thong_ke_hoi_dong' => $this->hoiDongs()
                ->withCount(['chiTietBaoCaos', 'phanCongVaiTros'])
                ->get()
                ->map(function($hoiDong) {
                    return [
                        'id' => $hoiDong->id,
                        'ten' => $hoiDong->ten,
                        'so_de_tai' => $hoiDong->chi_tiet_bao_caos_count,
                        'so_thanh_vien' => $hoiDong->phan_cong_vai_tros_count,
                        'trang_thai' => $hoiDong->trang_thai
                    ];
                }),
            'thong_ke_de_tai' => $this->deTais()
                ->with(['nhom', 'giangVien'])
                ->withCount('chiTietBaoCaos')
                ->get()
                ->map(function($deTai) {
                    return [
                        'id' => $deTai->id,
                        'ma_de_tai' => $deTai->ma_de_tai,
                        'tieu_de' => $deTai->tieu_de,
                        'nhom' => $deTai->nhom ? [
                            'id' => $deTai->nhom->id,
                            'ten' => $deTai->nhom->ten
                        ] : null,
                        'giang_vien' => $deTai->giangVien ? [
                            'id' => $deTai->giangVien->id,
                            'ten' => $deTai->giangVien->ten
                        ] : null,
                        'so_bao_cao' => $deTai->chi_tiet_bao_caos_count,
                        'trang_thai' => $deTai->trang_thai
                    ];
                })
        ];
    }

    public function getDanhSachHoiDong()
    {
        return $this->hoiDongs()
            ->with(['phanCongVaiTros.giangVien', 'chiTietBaoCaos.deTai'])
            ->get()
            ->map(function($hoiDong) {
                return [
                    'id' => $hoiDong->id,
                    'ten' => $hoiDong->ten,
                    'ma_hoi_dong' => $hoiDong->ma_hoi_dong,
                    'thanh_vien' => $hoiDong->phanCongVaiTros->map(function($phanCong) {
                        return [
                            'id' => $phanCong->giangVien ? $phanCong->giangVien->id : null,
                            'ten' => $phanCong->giangVien ? $phanCong->giangVien->ho_ten : null,
                            'vai_tro' => $phanCong->vaiTro ? $phanCong->vaiTro->ten : null
                        ];
                    }),
                    'de_tai' => $hoiDong->chiTietBaoCaos->map(function($chiTiet) {
                        return [
                            'id' => $chiTiet->deTai ? $chiTiet->deTai->id : null,
                            'ma_de_tai' => $chiTiet->deTai ? $chiTiet->deTai->ma_de_tai : null,
                            'tieu_de' => $chiTiet->deTai ? $chiTiet->deTai->tieu_de : null,
                            'trang_thai' => $chiTiet->trang_thai
                        ];
                    })
                ];
            });
    }

    public function getDanhSachDeTai()
    {
        return $this->deTais()
            ->with(['nhom', 'giangVien', 'chiTietBaoCaos.hoiDong'])
            ->get()
            ->map(function($deTai) {
                return [
                    'id' => $deTai->id,
                    'ma_de_tai' => $deTai->ma_de_tai,
                    'tieu_de' => $deTai->tieu_de,
                    'nhom' => $deTai->nhom ? [
                        'id' => $deTai->nhom->id,
                        'ten' => $deTai->nhom->ten
                    ] : null,
                    'giang_vien' => $deTai->giangVien ? [
                        'id' => $deTai->giangVien->id,
                        'ten' => $deTai->giangVien->ho_ten
                    ] : null,
                    'hoi_dong' => $deTai->chiTietBaoCaos->map(function($chiTiet) {
                        return [
                            'id' => $chiTiet->hoiDong ? $chiTiet->hoiDong->id : null,
                            'ten' => $chiTiet->hoiDong ? $chiTiet->hoiDong->ten : null,
                            'trang_thai' => $chiTiet->trang_thai
                        ];
                    })->first()
                ];
            });
    }
}

