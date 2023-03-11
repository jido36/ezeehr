<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function adminuser()
    {
        return $this->belongsToMany(AdminUser::class, 'admin_user_company');
    }
}
