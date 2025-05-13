<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DefenseSession extends Model
{
    use HasFactory;

    protected $table = 'defense_sessions';
    public $timestamps = false;

    protected $fillable = [
        'project_id', 'chairman_id', 'secretary_id',
        'schedule_date', 'venue',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function chairman()
    {
        return $this->belongsTo(Lecturer::class, 'chairman_id');
    }

    public function secretary()
    {
        return $this->belongsTo(Lecturer::class, 'secretary_id');
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class, 'defense_id');
    }

    public function committee()
    {
        return $this->belongsTo(DefenseCommittee::class, 'committee_id');
    }

}
