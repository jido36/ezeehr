<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\AdminUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Response;


class PagesController extends Controller
{
    public function dashboard()
    {
        // if (!Gate::allows('test-gate')) {
        //     abort(403);
        // }

        $response = Gate::inspect('test-gate');

        if ($response->allowed()) {
            // The action is authorized...
            $user = AdminUser::select('first_name', 'last_name', 'email')->where('id', Auth::id())->get();
            $company = AdminUser::find(Auth::id())->company()->get();
            $data = [
                'user' => $user,
                'company' => $company
            ];
            return response()->json(['status' => true, 'data' => $data], 200);
        } else {
            return response()->json(['status' => false, 'message' => $response->message()], 403);
        }
    }
}
