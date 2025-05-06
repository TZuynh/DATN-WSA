<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lecturer extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name', 'email', 'phone', 'department', 'office',
    ];

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function supervisedProjects()
    {
        return $this->hasMany(Project::class, 'supervisor_id');
    }

    public function coSupervisedProjects()
    {
        return $this->hasMany(Project::class, 'co_supervisor_id');
    }

    public function chairedSessions()
    {
        return $this->hasMany(DefenseSession::class, 'chairman_id');
    }

    public function secretarySessions()
    {
        return $this->hasMany(DefenseSession::class, 'secretary_id');
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class, 'evaluator_id');
    }
}
