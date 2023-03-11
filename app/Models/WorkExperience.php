<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkExperience extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'company',
        'description',
        'from',
        'to',
        'workhere',
        'location',
        'user_id'
    ];
}
