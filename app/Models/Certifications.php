<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certifications extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'organisation',
        'issue_date',
        'expiry_date',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }
}
