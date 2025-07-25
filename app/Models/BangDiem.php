<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BangDiem extends Model
{
    protected $fillable = [
        'giang_vien_id',
        'sinh_vien_id',
        'dot_bao_cao_id',
        'diem_bao_cao',
        'diem_bao_cao_hd',
        'diem_bao_cao_pb',
        'diem_thuyet_trinh',
        'diem_demo',
        'diem_cau_hoi',
        'diem_cong',
        'binh_luan'
    ];

    public function giangVien()
    {
        return $this->belongsTo(TaiKhoan::class, 'giang_vien_id');
    }

    public function sinhVien()
    {
        return $this->belongsTo(SinhVien::class);
    }

    public function dotBaoCao()
    {
        return $this->belongsTo(DotBaoCao::class);
    }

    public function nhom()
    {
        return $this->belongsTo(Nhom::class, 'nhom_id');
    }

    public function deTai()
    {
        return $this->belongsTo(DeTai::class, 'de_tai_id');
    }
}

