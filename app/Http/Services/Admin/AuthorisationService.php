<?php

namespace App\Http\Services\Admin;

use Illuminate\Http\Response;
use App\Models\Admin\Applications;
use App\Models\Admin\Company;
use App\Models\Admin\Role;
use App\Models\Admin\Vacancies;
use Illuminate\Support\Facades\Gate;


class AuthorisationService
{

    public function updateApplication()
    {
        $response = Gate::inspect('updateApplication', Applications::class);

        if (!$response->allowed()) {
            throw new \Exception($response->message());
        }
    }

    public function accessCompany()
    {
        $response = Gate::inspect('accessCompany', Company::class);

        if (!$response->allowed()) {
            throw new \Exception($response->message());
        }
    }

    public function createRole()
    {
        $response = Gate::inspect('createRole', Role::class);

        if (!$response->allowed()) {
            throw new \Exception($response->message());
        }
    }

    public function assignRole()
    {
        $response = Gate::inspect('assignRole', Role::class);

        if (!$response->allowed()) {
            throw new \Exception($response->message());
        }
    }

    // updateJobs

    public function viewJobs()
    {

        $response = Gate::inspect('viewJobs', Vacancies::class);

        if (!$response->allowed()) {
            throw new \Exception($response->message());
        }
    }

    public function updateJobs()
    {

        $response = Gate::inspect('updateJobs', Vacancies::class);

        if (!$response->allowed()) {
            throw new \Exception($response->message());
        }
    }
}
