<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectMember extends Model
{
    use HasFactory;

    protected $table = 'project_members';
    public $timestamps = false;

    protected $fillable = [
        'group_id', 'student_id', 'role', 'joined_at',
    ];

    public function group()
    {
        return $this->belongsTo(ProjectGroup::class, 'group_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
