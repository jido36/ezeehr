<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vacancies extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'skill', 'status', 'entity_id', 'admin_user_id'
    ];

    public function applications(): HasMany
    {
        return $this->hasMany(Applications::class, 'job_id');
    }
}
