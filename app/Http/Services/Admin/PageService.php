<?php

namespace App\Http\Services\Admin;

use App\Models\Admin\AdminUser;
use Illuminate\Support\Facades\Auth;

class PageService
{
    public function dashboard()
    {
        $user = AdminUser::select('first_name', 'last_name', 'email')->where('id', Auth::id())->get();
        $company = AdminUser::find(Auth::id())->company()->get();
        $data = [
            'user' => $user,
            'company' => $company
        ];
        return $data;
    }
}
