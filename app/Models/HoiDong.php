<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HoiDong extends Model
{
    use HasFactory;

    protected $table = 'hoi_dongs';

    protected $fillable = [
        'ma_hoi_dong',
        'ten',
        'dot_bao_cao_id',
        'phong_id'
    ];

    /**
     * Tạo mã hội đồng tự động
     */
    public static function taoMaHoiDong($dotBaoCaoId)
    {
        $dotBaoCao = DotBaoCao::findOrFail($dotBaoCaoId);
        $count = self::where('dot_bao_cao_id', $dotBaoCaoId)->count() + 1;
        return 'HD' . $dotBaoCao->nam_hoc . str_pad($count, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Lấy đợt báo cáo của hội đồng
     */
    public function dotBaoCao()
    {
        return $this->belongsTo(DotBaoCao::class);
    }

    /**
     * Lấy danh sách chi tiết báo cáo của hội đồng
     */
    public function chiTietBaoCaos()
    {
        return $this->hasMany(ChiTietDeTaiBaoCao::class);
    }

    /**
     * Lấy danh sách lịch chấm của hội đồng
     */
    public function lichChams()
    {
        return $this->hasMany(LichCham::class);
    }

    /**
     * Lấy danh sách phân công vai trò của hội đồng
     */
    public function phanCongVaiTros()
    {
        return $this->hasMany(PhanCongVaiTro::class);
    }

    /**
     * Lấy phòng của hội đồng
     */
    public function phong()
    {
        return $this->belongsTo(Phong::class);
    }
}

