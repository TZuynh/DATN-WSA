<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name', 'email', 'phone', 'class', 'year',
    ];

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function projectMembers()
    {
        return $this->hasMany(ProjectMember::class, 'student_id');
    }

    public function projectGroups()
    {
        return $this->belongsToMany(
            ProjectGroup::class,
            'project_members',
            'student_id',
            'group_id'
        )->withPivot('role', 'joined_at');
    }
}
