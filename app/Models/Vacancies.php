<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vacancies extends Model
{
    use HasFactory;

    public function applications()
    {
        return $this->hasMany(Applications::class, 'job_id');
    }
}
