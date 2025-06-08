<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lop extends Model
{
    use HasFactory;

    protected $table = 'lops';

    protected $fillable = [
        'ten_lop',

    ];

    public function sinhViens()
    {
        return $this->hasMany(SinhVien::class, 'lop_id');
    }
} 