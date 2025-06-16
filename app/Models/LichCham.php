<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LichCham extends Model
{
    protected $table = 'lich_chams';

    protected $fillable = [
        'hoi_dong_id',
        'dot_bao_cao_id',
        'nhom_id',
        'de_tai_id',
        'phan_cong_cham_id',
        'lich_tao'
    ];

    /**
     * Lấy hội đồng của lịch chấm
     */
    public function hoiDong(): BelongsTo
    {
        return $this->belongsTo(HoiDong::class);
    }

    /**
     * Lấy đợt báo cáo của lịch chấm
     */
    public function dotBaoCao(): BelongsTo
    {
        return $this->belongsTo(DotBaoCao::class);
    }

    /**
     * Lấy nhóm của lịch chấm
     */
    public function nhom(): BelongsTo
    {
        return $this->belongsTo(Nhom::class);
    }

    /**
     * Lấy đề tài của lịch chấm
     */
    public function deTai(): BelongsTo
    {
        return $this->belongsTo(DeTai::class);
    }

    /**
     * Lấy phân công chấm của lịch chấm
     */
    public function phanCongCham(): BelongsTo
    {
        return $this->belongsTo(PhanCongCham::class);
    }
}

