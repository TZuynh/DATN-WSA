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
        'ten', 'email', 'mat_khau', 'vai_tro',
    ];

    protected $hidden = ['mat_khau'];

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
        return $this->hasMany(Nhom::class, 'giang_vien_id');
    }

    public function bangDiems()
    {
        return $this->hasMany(BangDiem::class, 'giang_vien_id');
    }
}

