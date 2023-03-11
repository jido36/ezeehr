<?php

namespace App\Policies\Admin;

use App\Models\Admin\AdminUser;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class RolePolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function createRole(AdminUser $user)
    {
        return in_array("admin", $user->getRoles())
            ? Response::allow()
            : Response::deny('You do have permission to create roles');;
    }
}
