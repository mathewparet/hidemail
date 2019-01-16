<?php

namespace App\Providers;

use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

use App\Email;

use App\Policies\EmailPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Email::class => EmailPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();

        Passport::tokensCan([
            'view-hidden-email-ids' => 'View your hidden email Ids',
            'create-hidden-email-ids' => 'Create hidden email Ids',
            'delete-hidden-email-ids' => 'Delete your hidden email Ids'
        ]);

        Passport::setDefaultScope(['view-hidden-email-ids']);
    }
}
