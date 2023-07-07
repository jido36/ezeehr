<?php

namespace App\Providers;

use App\Models\Admin\Role;
use App\Models\Admin\Company;
use Laravel\Passport\Passport;
use App\Models\Admin\AdminUser;
use App\Models\Admin\Vacancies;
use App\Models\Admin\Applications;
use App\Policies\Admin\JobsPolicy;
use App\Policies\Admin\RolePolicy;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Gate;

// use Illuminate\Support\Facades\Gate;
use App\Policies\Admin\CompanyPolicy;
use App\Policies\Admin\ApplicationsPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        Role::class => RolePolicy::class,
        Applications::class => ApplicationsPolicy::class,
        Vacancies::class => JobsPolicy::class,
        Company::class => CompanyPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::tokensExpireIn(now()->addDays(1));
        Gate::define('test-gate', function (AdminUser $adminuser) {
            return $adminuser->id === 1
                ? Response::allow()
                : Response::deny('You must be an administrator.');
        });

        //
    }
}
