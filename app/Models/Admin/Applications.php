<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Applications extends Model
{
    use HasFactory;

    protected $table = 'applications';

    protected $fillable = [
        'status',
        'id',
        'job_id',
        'application_id',
        'cv_id',
        'cover_letter',
        'applicant_id'
    ];

    public function comments(): HasMany
    {
        return $this->HasMany(Comment::class, 'application_id');
    }
}
