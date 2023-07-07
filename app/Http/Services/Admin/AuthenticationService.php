<?php

namespace App\http\Services\Admin;

use App\Models\Admin\AdminUser;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Response;

class AuthenticationService
{

    public function login($validated)
    {
        try {
            //code...
            $response = Http::asForm()->post(env('APP_URL') . '/oauth/token', [
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

        $ret_response = $response->json();

        if (isset($ret_response['error'])) {
            return response()->json(['status' => false, 'message' => 'User name or password incorrect'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }


        $userdata = AdminUser::where('email', $validated['email'])->first();

        // print_r($userdata);
        // die;

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
            'data' => $ret_response,
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
