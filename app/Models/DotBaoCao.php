<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DotBaoCao extends Model
{
    protected $fillable = ['nam_hoc'];

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

