<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Applications extends Model
{
    use HasFactory;

    /**!
     *       'job_id' => $validated['job_id'],
            'application_id' => $validated['application_id'],
            'cv_id' => $validated['cv_id'],
            'cover_letter_id' => $validated['cover_letter_id'],
            'applicant_id' => Auth::id()
     */

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
