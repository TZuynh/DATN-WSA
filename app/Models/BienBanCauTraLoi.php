<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BienBanCauTraLoi extends Model
{
    protected $table = 'bien_ban_cau_tra_lois';
    protected $fillable = [
        'bien_ban_nhan_xet_id',
        'cau_hoi',
    ];

    public function bienBanNhanXet()
    {
        return $this->belongsTo(BienBanNhanXet::class);
    }
} 