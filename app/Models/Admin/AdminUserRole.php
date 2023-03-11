<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminUserRole extends Model
{
    use HasFactory;

    protected $table = "admin_user_role";

    protected $fillable = ['admin_user_id', 'role_id'];
}
