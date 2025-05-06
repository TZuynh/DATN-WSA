<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'defense_id', 'evaluator_id', 'score', 'comments',
    ];

    public function defenseSession()
    {
        return $this->belongsTo(DefenseSession::class, 'defense_id');
    }

    public function evaluator()
    {
        return $this->belongsTo(Lecturer::class, 'evaluator_id');
    }
}
