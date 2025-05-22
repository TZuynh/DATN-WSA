<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DotDeTai extends Model
{
    protected $table = 'dot_de_tais';

    protected $fillable = [
        'de_tai_id', 'giang_vien_id'
    ];

    public function deTai()
    {
        return $this->belongsTo(DeTai::class);
    }

    public function giangVien()
    {
        return $this->belongsTo(TaiKhoan::class, 'giang_vien_id');
    }
}

