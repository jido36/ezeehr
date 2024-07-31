<?php

namespace App\Http\Services\Admin;

use Exception;
use App\Models\Admin\Role;
use App\Models\Admin\AdminUser;
use App\Models\Admin\AdminUserRole;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class RoleService
{
    public function createRole($request)
    {
        $validated = $request->validate(
            [
                'role' => 'required|string',
            ]
        );
        // The action is authorized...
        $data = [
            'role' => strtolower($validated['role']),
        ];

        try {
            Role::create($data);
            return response()->json(['status' => true, 'message' => 'Role created'], 200);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json(['status' => true, 'message' => 'Role created'], 403);
        }
    }

    public function assignRole($request)
    {

        // $id = Auth::id();

        $validated = $request->validate(
            [
                "role_id" => "required|integer",
                "admin_id" => "required|integer"
            ]
        );

        $data = [
            'admin_user_id' => $validated['admin_id'],
            'role_id' => $validated['role_id']
        ];

        $id = $validated['admin_id'];

        $checkRole = AdminUser::find($id)->roles()->where('role_id', $validated['role_id'])->get();
        if ($checkRole->count() > 0) {
            return response()->json(['status' => true, 'message' => 'Role previously assigned to user'], 200);
        }

        try {

            AdminUserRole::create($data);
            return response()->json(['status' => true, 'message' => 'Role assigned successfully'], 200);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json(['status' => false, 'message' => 'Error Occured'], 403);
        }
    }
}
