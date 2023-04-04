<?php

namespace App\Policies\Admin;

use App\Models\Admin\AdminUser;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class AdminUserPolicy
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


    public function before(AdminUser $user): bool|null
    {
        if (in_array("admin", $user->getRoles())) {
            return true;
        }

        return null;
    }

    public function hr(AdminUser $user)
    {
        return in_array("hr", $user->getRoles())
            ? Response::allow()
            : Response::deny('You do not have permission to create roles');
    }
}
