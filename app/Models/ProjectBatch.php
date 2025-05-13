<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectBatch extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'year'];

    public function projects()
    {
        return $this->hasMany(Project::class, 'batch_id');
    }
}
