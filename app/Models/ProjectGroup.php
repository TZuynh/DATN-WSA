<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_name', 'year',
    ];

    public function project()
    {
        return $this->hasOne(Project::class, 'group_id');
    }

    public function members()
    {
        return $this->hasMany(ProjectMember::class, 'group_id');
    }

    public function students()
    {
        return $this->belongsToMany(
            Student::class,
            'project_members',
            'group_id',
            'student_id'
        )->withPivot('role', 'joined_at');
    }
}
