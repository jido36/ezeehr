<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
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

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
            'client_secret' => '',
            'client_id' => '',
        ]);

        $validated = $validator->validated();

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => 'Error validating input', 'errors' => $validator->errors()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        try {
            //code...
            $response = Http::asForm()->post(env('APP_URL').'/oauth/token', [
                'grant_type' => 'password',
                'client_id' => env('PASSPORT_ADMIN_CLIENT_ID'),
                'client_secret' => env('PASSPORT_ADMIN_CLIENT_SECRET'),
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

    public function authenticate(Request $request)
    {
        // echo "hello world";
        // die;
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // $user = AdminUser::where('email', $request->email)->first();
        //     $token =  $user->createToken('Token Name')->accessToken;

        //     echo $token; die;
        

        $credentials = $request->only('email', 'password');

        if (Auth::guard('api-admin')->attempt($credentials)) {
            // if (Auth::attempt($credentials)) {
            // if (Auth::guard('admin')->attempt($credentials)) {
            // $request->session()->regenerate();
            // echo "here";
            // die;
            // $response = Http::get('http://easyHr.test:8081/oauth/tokens');

            // return $response->json();
            // $user = AdminUser::where('email', $request->email)->first();
            // $token =  $user->createToken('Token Name')->accessToken;
            return response()->json(['status' => true, 'message' => 'authentication successful', 'token' => $token], Response::HTTP_OK);
        }

        return response()->json(['status' => false, 'message' => 'email or password incorrect'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
