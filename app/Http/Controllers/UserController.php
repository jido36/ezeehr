<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class UserController extends Controller
{
    //
    public $name;
    public $email;
    public $password;
    public $confirm_password;

    public function create(Request $request)
    {
        // print_r($request->all());
        // die;
        $validator = Validator::make($request->all(), [
            // 'name' => 'required|max:255',
            'email' => 'required|email|unique:users|max:50',
            'confirm_email' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => 'Error validating input', 'errors' => $validator->errors()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Retrieve the validated input...
        $validated = $validator->validated();
        $data = [
            // 'name' => $validated['name'],
            'email' => $validated['email'],
            'confirm_email' => $validated['confirm_email'],
            'password' => Hash::make($validated['password'])
        ];


        try {
            User::create($data);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['status' => false, 'message' => 'Error validating input', 'errors' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(['status' => true, 'message' => 'beneficiary saved', 'data' => $request->all()], Response::HTTP_OK);

        // print_r($validated);
        // die;

        // if ($validated['fails']) {
        //     return response(['status' => false, 'message' => 'Validation errors. ' .  $validator->errors(), 'data' => false], 422);
        // }
    }

    // public function login(Request $request)
    // {

    //     $validator = Validator::make($request->all(), [
    //         'email' => 'required|email',
    //         'password' => 'required'
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['status' => false, 'message' => 'Error validating input', 'errors' => $validator->errors()], Response::HTTP_INTERNAL_SERVER_ERROR);
    //     }

    //     $user = User::where('email', $request->email)->first();

    //     if (!$user || !Hash::check($request->password, $user->password)) {
    //         return response()->json(['status' => false, 'message' => 'email or password incorrect'], Response::HTTP_INTERNAL_SERVER_ERROR);
    //     }

    //     $token =  $user->createToken('Token Name')->accessToken;
    //     return response()->json(['status' => true, 'message' => 'access token generated successfully', 'token' => $token], Response::HTTP_OK);
    // }

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
                'client_id' => env('PASSPORT_CANDIDATE_CLIENT_ID'),
                'client_secret' => env('PASSPORT_CANDIDATE_CLIENT_SECRET'),
                'username' => $validated['email'],
                'password' => $validated['password'],
                'scope' => '',
            ]);
        } catch (\Exception $e) {
            //throw $th;
            Log::error($e);
            return response()->json(['status' => false, 'message' => 'User name or password incorrect'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $userdata = User::where('email', $validated['email'])->first();

        $ret_userdata = [
            "id" => $userdata['id'],
            "email" => $userdata['email'],
            "created_at" => $userdata['created_at'],
            "updated_at" => $userdata['updated_at'],
            // "entity_id" => $userdata['entity_id'],
            // "role" => "admin"
        ];

        $ret_response = [
            'data' => $response->json(),
            'userdata' => $ret_userdata,
            // 'userability' => [
            //     [
            //         "action" => "manage",
            //         "subject" => "all"
            //     ]
            // ]
        ];


        return response()->json($ret_response);
    }

    public function authenticate(Request $request)
    {

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            // $request->session()->regenerate();
            // echo "here";
            // die;
            // $response = Http::get('http://easyHr.test:8081/oauth/tokens');

            // return $response->json();
            $user = User::where('email', $request->email)->first();
            $token =  $user->createToken('Token Name')->accessToken;
            return response()->json(['status' => true, 'message' => 'authentication successful', 'token' => $token], Response::HTTP_OK);
        }

        return response()->json(['status' => false, 'message' => 'email or password incorrect'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function getToken(Request $request)
    {

        // $credentials = $request->validate([
        //     'email' => ['required', 'email'],
        //     'password' => ['required'],
        // ]);

        $response = Http::asForm()->post('http://easyHr.test:8081/oauth/token', [
            'grant_type' => 'password',
            'client_id' => '8',
            'client_secret' => '5KQekgu2Xc0D8t3dG86XOYmoIN4Gyts7WHyLDAfr',
            // 'client_id' => '7',
            // 'client_secret' => 'vXfN0rPmkgDKuJ6WmwKc7JQVay4xkAgUVMRSS72s',
            'username' => 'okitikanolugbenga@gmail.com',
            'password' => 'hello world',
            'scope' => '',
        ]);

        return $response->json();
    }

    public function logout(Request $request)
    {

        $request->user()->currentAccessToken()->delete();
        return response()->json(['status' => true, 'message' => 'user logged out'], Response::HTTP_OK);
    }

    public function getClients(Request $request)
    {
        // print_r($request->all());
        // die;
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);

        if (Auth::attempt($credentials)) {

            $response = Http::get('http://ezeehr.test/oauth/token');
            // print_r($response);
            return $response->json();

            // return response()->json(['status' => true, 'message' => 'login attempt successful']);
        }

        return response()->json(['status' => false, 'message' => 'email or password incorrect'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
