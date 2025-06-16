<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Phong extends Model
{
    protected $table = 'phongs';

    protected $fillable = [
        'ten_phong'
    ];

    /**
     * Lấy danh sách hội đồng của phòng
     */
    public function hoiDongs()
    {
        return $this->hasMany(HoiDong::class);
    }
} 