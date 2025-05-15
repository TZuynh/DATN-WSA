<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DotBaoCao extends Model
{
    protected $fillable = ['hoc_ky', 'khoa_hoc'];

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
}

