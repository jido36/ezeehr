<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    // protected $guarded = ['id'];
    protected $primaryKey = 'entity_id';

    public function adminuser()
    {
        return $this->belongsToMany(AdminUser::class, 'admin_user_company');
    }

    public function applications()
    {
        return $this->hasManyThrough(
            Applications::class,
            Jobs::class,
            'entity_id',
            'job_id',
            'entity_id',
            'id'
        );
    }
}
