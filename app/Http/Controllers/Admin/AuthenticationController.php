<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\Admin\AdminUser;
use Illuminate\Http\Response;






class AuthenticationController extends Controller
{
    public function login(Request $request)
    {
        // print_r($request->all());
        // die;
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
            'client_secret' => 'required',
            'client_id' => '',
        ]);

        $validated = $validator->validated();

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => 'Error validating input', 'errors' => $validator->errors()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        try {
            //code...
            $response = Http::asForm()->post('http://easyHr.test:8081/oauth/token', [
                'grant_type' => 'password',
                'client_id' => $validated['client_id'],
                'client_secret' => $validated['client_secret'],
                // 'client_id' => '7',
                // 'client_secret' => 'vXfN0rPmkgDKuJ6WmwKc7JQVay4xkAgUVMRSS72s',
                'username' => $validated['email'],
                'password' => $validated['password'],
                'scope' => '',
            ]);
        } catch (\Exception $e) {
            //throw $th;
            Log::error($e);
            return response()->json(['status' => false, 'message' => 'User name or password incorrect'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $userdata = AdminUser::where('email', $validated['email'])->first();

        $ret_userdata = [
            "id" => $userdata['id'],
            "first_name" => $userdata['first_name'],
            "last_name" => $userdata['last_name'],
            "email" => $userdata['email'],
            "created_at" => $userdata['created_at'],
            "updated_at" => $userdata['updated_at'],
            "entity_id" => $userdata['entity_id'],
            "role" => "admin"
        ];

        $ret_response = [
            'data' => $response->json(),
            'userdata' => $ret_userdata,
            'userability' => [
                [
                    "action" => "manage",
                    "subject" => "all"
                ]
            ]
        ];


        return response()->json($ret_response);
    }
}
