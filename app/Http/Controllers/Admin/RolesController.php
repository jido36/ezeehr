<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\AdminUserRole;
use App\Models\Admin\Role;
use Exception;
use Illuminate\Support\Facades\Log;


class RolesController extends Controller
{
    public function createRole(Request $request)
    {
        $validated = $request->validate(
            [
                'role' => 'required|string',
            ]
        );
        // print_r($request->all());
        // die;

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

    public function assignRole(Request $request)
    {
        // $response = Gate::inspect('createRole');
        $this->authorize('createRole', Role::class);

        // if ($response->allowed()) {
        // The action is authorized...
        $validated = $request->validate(
            [
                "admin_id" => "required|integer",
                "role_id" => "required|integer"
            ]
        );
        // print_r($request->all());
        // die;

        $data = [
            'admin_user_id' => $validated['admin_id'],
            'role_id' => $validated['role_id']
        ];

        try {
            AdminUserRole::create($data);
            return response()->json(['status' => true, 'message' => 'Role assigned successfully'], 200);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json(['status' => true, 'message' => 'Error Occured'], 403);
        }
        // } else {
        //     echo $response->message();
        // }
    }
}
