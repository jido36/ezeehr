<?php

namespace App\Http\Services\Admin;

use Illuminate\Support\Str;
use Illuminate\Http\Response;
use App\Models\Admin\AdminUser;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin\AdminUserCompany;
use App\Models\Admin\Company;

class AdminUserService
{
    public function createUser($validated)
    {
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

        return response()->json(['status' => true, 'message' => 'beneficiary saved'], Response::HTTP_OK);
    }

    public function adduser($validated)
    {
        $user = Auth::user();
        $data = [
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'confirm_email' => $validated['confirm_email'],
            'password' => Hash::make($validated['password']),
            'entity_id' => $user->entity_id
        ];

        $company = Company::find($validated['company']);

        try {
            $adminUser = adminUser::create($data);
            if (isset($validated['company'])) {
                AdminUserCompany::create([
                    'admin_user_id' => $adminUser->id,
                    'company_id' => $company->id,
                    'entity_id' => $user->entity_id,
                    'company_entity_id' => $validated['company']
                ]);
            }
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['status' => false, 'message' => 'Error validating input', 'errors' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(['status' => true, 'message' => 'User created'], Response::HTTP_OK);
    }
}
