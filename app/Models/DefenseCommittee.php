<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DefenseCommittee extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function members()
    {
        return $this->hasMany(DefenseCommitteeMember::class, 'committee_id');
    }

    public function sessions()
    {
        return $this->hasMany(DefenseSession::class, 'committee_id');
    }
}
