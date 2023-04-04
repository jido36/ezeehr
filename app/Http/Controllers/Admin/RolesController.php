<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\AdminUserRole;
use App\Models\Admin\AdminUser;
use App\Models\Admin\Role;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;


class RolesController extends Controller
{

    public function createRole(Request $request)
    {
        // $this->authorize('createRole', Role::class);

        $response = Gate::inspect('createRole', Role::class);

        $validated = $request->validate(
            [
                'role' => 'required|string',
            ]
        );

        if ($response->allowed()) {
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
        } else {
            echo $response->message();
        }
    }

    public function assignRole(Request $request)
    {
        $id = Auth::id();
        $response = Gate::inspect('assignRole', Role::class);

        if ($response->allowed()) {
            $validated = $request->validate(
                [
                    "role_id" => "required|integer"
                ]
            );

            $data = [
                'admin_user_id' => $id,
                'role_id' => $validated['role_id']
            ];

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
        } else {
            echo $response->message();
        }
    }
}
