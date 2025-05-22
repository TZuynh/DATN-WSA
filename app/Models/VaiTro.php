<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VaiTro extends Model
{
    protected $fillable = ['ten'];

    public function phanCongVaiTros()
    {
        return $this->hasMany(PhanCongVaiTro::class);
    }
}
