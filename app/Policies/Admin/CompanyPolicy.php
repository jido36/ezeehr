<?php

namespace App\Policies\Admin;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\Admin\AdminUser;
use Illuminate\Auth\Access\Response;

class CompanyPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    public function accessCompany(Adminuser $user)
    {
        return in_array("admin", $user->getRoles())
            ? Response::allow()
            : Response::deny('You do not have permission to make modification(s) to the company record');
    }
}
