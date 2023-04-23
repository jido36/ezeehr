<?php

namespace App\Providers;

use App\Models\Admin\AdminUser;
use App\Models\Admin\Company;
use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Access\Response;
use App\Models\Admin\Role;
use App\Policies\Admin\RolePolicy;
use App\Models\Admin\Applications;
use App\Policies\Admin\ApplicationsPolicy;
use App\Models\Admin\Jobs;
use App\Policies\Admin\JobsPolicy;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

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
        Jobs::class => JobsPolicy::class
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
