<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class TaiKhoan extends Authenticatable
{
    use HasApiTokens;
    use Notifiable;

    protected $table = 'tai_khoans';

    protected $fillable = [
        'ten',
        'email',
        'mat_khau',
        'vai_tro',
    ];

    protected $hidden = [
        'mat_khau',
    ];

    protected $casts = [
        'vai_tro' => 'string',
    ];

    public function getAuthPassword()
    {
        return $this->mat_khau;
    }

    public function getAuthIdentifierName()
    {
        return 'id';
    }

    public function getAuthIdentifier()
    {
        return $this->id;
    }

    public function nhoms()
    {
        return $this->belongsToMany(Nhom::class, 'chi_tiet_nhoms', 'sinh_vien_id', 'nhom_id');
    }

    public function bangDiems()
    {
        return $this->hasMany(BangDiem::class, 'giang_vien_id');
    }

    public function deTais()
    {
        return $this->hasMany(DeTai::class, 'giang_vien_id');
    }

    public function nhomsHuongDan()
    {
        return $this->hasMany(Nhom::class, 'giang_vien_id');
    }
}

