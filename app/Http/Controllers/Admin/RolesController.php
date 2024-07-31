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
use App\Http\Services\Admin\AuthorisationService;
use App\Http\Services\Admin\RoleService;


class RolesController extends Controller
{

    public function createRole(Request $request, AuthorisationService $authorisationservice, RoleService $roleService)
    {
        // $this->authorize('createRole', Role::class);

        try {
            $authorisationservice->createRole();
        } catch (Exception $e) {
            abort(422, $e->getMessage());
        }

        return $roleService->createRole($request);
    }

    public function assignRole(Request $request, AuthorisationService $authorisationservice, RoleService $roleservice)
    {

        try {
            $authorisationservice->createRole();
        } catch (Exception $e) {
            abort(422, $e->getMessage());
        }

        return $roleservice->assignRole($request);
    }
}
