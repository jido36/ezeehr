<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandidatesBio extends Model
{
    use HasFactory;
    protected $table = 'candidates_bio';
    protected $fillable = [
        'first_name',
        'last_name',
        'dob',
        'sex',
        'cid',
    ];
}
