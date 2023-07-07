<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\AdminUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Response;
use App\Http\Services\Admin\PageService;


class PagesController extends Controller
{
    public function dashboard(PageService $pageservice)
    {
        // The action is authorized...
        $data = $pageservice->dashboard();
        return response()->json(['status' => true, 'data' => $data], 200);
    }
}
