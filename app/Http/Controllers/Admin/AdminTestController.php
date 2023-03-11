<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\AdminUser;
use Exception;
use Illuminate\Support\Facades\Log;

class AdminTestController extends Controller
{
    public function dashboard()
    {
        // if (!Gate::allows('test-gate')) {
        //     abort(403);
        // }

        // $response = Gate::inspect('test-gate');

        try {
            // The action is authorized...
            $user = AdminUser::select('id', 'first_name', 'last_name', 'email')->where('id', 1)->get();
            $company = AdminUser::find(1)->company()->get();
            $data = [
                'user' => $user,
                'company' => $company
            ];
            return response()->json(['status' => true, 'data' => $data], 200);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json(['status' => false, 'message' => "error getting records"], 403);
        }
    }
}
