<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeTaiMau extends Model
{
    use HasFactory;

    protected $table = 'de_tai_maus';

    protected $fillable = [
        'ten',
    ];

    public function deTais()
    {
        return $this->hasMany(DeTai::class, 'de_tai_mau_id');
    }
} 