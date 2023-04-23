<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminUserCompany extends Model
{
    use HasFactory;

    protected $table = 'admin_user_company';

    protected $fillable = [
        'admin_user_id',
        'company_id',
        'entity_id',
        'company_entity_id'
    ];
}
