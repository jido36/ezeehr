<?php

namespace App\Models\Admin;

use App\Models\Admin\Vacancies;
use App\Models\CandidatesBio;
use App\Models\Documents;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
        return $this->hasMany(Comment::class, 'application_id',);
    }

    public function certifications(): HasMany
    {
        return $this->hasMany(Certifications::class, 'user_id', 'applicant_id');
    }

    public function vacancies(): BelongsTo
    {
        return $this->belongsTo(Vacancies::class, 'job_id');
    }

    public function candidateBio(): HasOne
    {
        return $this->hasOne(CandidatesBio::class, 'id', 'applicant_id');
    }

    public function applicationCv(): HasOne
    {
        return $this->hasOne(Documents::class, 'id', 'cv_id');
    }

    public function applicationCoverLetter(): HasOne
    {
        return $this->hasOne(Documents::class, 'id', 'coverletter_id');
    }
}
