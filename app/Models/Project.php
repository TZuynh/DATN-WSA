<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id', 'supervisor_id', 'co_supervisor_id',
        'status_id', 'title', 'description',
        'proposal_date', 'start_date', 'target_end_date', 'actual_end_date',
    ];

    public function group()
    {
        return $this->belongsTo(ProjectGroup::class, 'group_id');
    }

    public function supervisor()
    {
        return $this->belongsTo(Lecturer::class, 'supervisor_id');
    }

    public function coSupervisor()
    {
        return $this->belongsTo(Lecturer::class, 'co_supervisor_id');
    }

    public function status()
    {
        return $this->belongsTo(ProjectStatus::class, 'status_id');
    }

    public function members()
    {
        return $this->hasMany(ProjectMember::class, 'group_id', 'group_id');
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'project_id');
    }

    public function defenseSession()
    {
        return $this->hasOne(DefenseSession::class, 'project_id');
    }

    public function evaluations()
    {
        return $this->hasManyThrough(
            Evaluation::class,
            DefenseSession::class,
            'project_id',
            'defense_id',
            'id',
            'defense_id'
        );
    }

    public function batch()
    {
        return $this->belongsTo(ProjectBatch::class, 'batch_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(Lecturer::class, 'reviewer_id');
    }

}
