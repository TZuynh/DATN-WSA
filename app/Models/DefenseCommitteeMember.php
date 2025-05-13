<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DefenseCommitteeMember extends Model
{
    use HasFactory;

    protected $fillable = ['committee_id', 'lecturer_id', 'role'];
    public function committee()
    {
        return $this->belongsTo(DefenseCommittee::class);
    }

    public function lecturer()
    {
        return $this->belongsTo(Lecturer::class);
    }
}
