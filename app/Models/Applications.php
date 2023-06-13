<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Applications extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'job_id',
        'application_id',
        'cv_id',
        'cover_letter',
        'applicant_id',
        'status'
    ];
}
