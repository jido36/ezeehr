<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $table = 'application_comments';

    protected $fillable = [
        'application_id', 'comments', 'admin_id'
    ];
}
