<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeTaiMau extends Model
{
    use HasFactory;

    protected $table = 'de_tai_mau';

    protected $fillable = [
        'ten_de_tai',
        'mo_ta',
        'yeu_cau',
        'tai_lieu_tham_khao',
        'so_luong_sinh_vien',
        'trang_thai'
    ];
} 