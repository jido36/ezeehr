<?php

namespace App\Http\Services\Admin;

use Exception;
use App\Models\Admin\Company;
use App\Models\Admin\AdminUser;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin\AdminUserCompany;
use Illuminate\Support\Facades\Validator;

class CompanyService
{
    public function createCompany($validated)
    {

        $user = AdminUser::find(Auth::id())->first();

        $data = [
            'name' => $validated['name'],
            'size' => $validated['size'],
            'email' => $validated['email'],
            'created_by' => Auth::id(),
            'entity_id' => $user->entity_id,
        ];

        try {
            $company = Company::create($data);
            return response(['status' => true, 'message' => 'Company created'], 200);
        } catch (Exception $e) {
            Log::error($e);
            return response(['status' => false, 'message' => 'Error creating record'], 422);
        }
    }

    public function assignCompany($request)
    {
        $method = $request->method();
        $user = Auth::user();
        if ($method === "GET") {
            try {
                $companies = Company::select('name', 'id', 'entity_id')->where('entity_id', $user->entity_id)->get();
                $admin_users = AdminUser::selectRaw('id, CONCAT(admin_users.first_name, " ", admin_users.last_name) AS full_name')->where('entity_id', $user->entity_id)->get();
            } catch (\Exception $e) {
                Log::error($e);
                return response(['status' => false, 'message' => 'Error getting record'], 422);
            }
            $data = [
                'companies' => $companies,
                'admin_users' => $admin_users
            ];
            return response(['status' => true, 'data' => $data], 200);
        } else {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|integer',
                'company_id' => 'required|integer'
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => 'All fields are required.', 'errors' => $validator->errors()], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            $validated = $validator->validated();
            $company = Company::select('id', 'name', 'entity_id')->where('id', $validated['company_id'])->get()->first();

            $data = [
                'admin_user_id' => $validated['user_id'],
                'company_id' => $validated['company_id'],
                'entity_id' => $user->entity_id,
                'company_entity_id' => $company->entity_id
            ];

            // check if user has the company assigned.
            $checkAssignment = AdminUserCompany::where('company_id', $validated['company_id'])
                ->where('admin_user_id', $validated['user_id'])->get();

            if ($checkAssignment->count() > 0) {
                return response(['status' => true, 'message' => 'User belongs to the company'], 409);
            }

            try {
                AdminUserCompany::create($data);
            } catch (\Exception $e) {
                Log::error($e);
                return response(['status' => false, 'message' => 'Error getting record'], 422);
            }
            return response(['status' => true, 'message' => "User added to company successfully"], 200);
        }
    }
}
