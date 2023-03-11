<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin\AdminUser;
use App\Models\Admin\AdminUserCompany;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;
use App\Models\Admin\AdminUserRole;


class AdmiUserController extends Controller
{
    //

    public function createUser(Request $request)
    {
        // print_r($request->all());
        // die;
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email',
            'confirm_email' => 'required|string',
            'password' => 'required|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => 'Error validating input', 'errors' => $validator->errors()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Retrieve the validated input...
        $validated = $validator->validated();
        $data = [
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'confirm_email' => $validated['confirm_email'],
            'password' => Hash::make($validated['password']),
            'entity_id' => Str::uuid()
        ];

        try {
            adminUser::create($data);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['status' => false, 'message' => 'Error validating input', 'errors' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(['status' => true, 'message' => 'beneficiary saved', 'data' => $request->all()], Response::HTTP_OK);
    }

    // adds a new user to admins company
    public function addUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email',
            'confirm_email' => 'required|string',
            'company' => 'nullable|integer',
            'password' => 'required|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => 'Error validating input', 'errors' => $validator->errors()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Retrieve the validated input...
        $validated = $validator->validated();
        $user = Auth::user();
        $data = [
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'confirm_email' => $validated['confirm_email'],
            'password' => Hash::make($validated['password']),
            'entity_id' => $user->entity_id
        ];

        try {
            $adminUser = adminUser::create($data);
            if (isset($validated['company'])) {
                AdminUserCompany::create([
                    'admin_user_id' => $adminUser->id,
                    'company_id' => $validated['company'],
                ]);
            }
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['status' => false, 'message' => 'Error validating input', 'errors' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(['status' => true, 'message' => 'beneficiary saved', 'data' => $request->all()], Response::HTTP_OK);
    }
}
