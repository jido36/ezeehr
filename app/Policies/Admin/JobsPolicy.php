<?php

namespace App\Policies\Admin;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\Admin\AdminUser;
use Illuminate\Auth\Access\Response;

class JobsPolicy
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

    public function viewJobs(Adminuser $user)
    {
        return in_array("hr", $user->getRoles())
            ? Response::allow()
            : Response::deny('You do not have permission to update the record');
    }

    public function updateJobs(Adminuser $user)
    {
        return in_array("hr", $user->getRoles())
            ? Response::allow()
            : Response::deny('You do not have permission to update the record');
    }
}
