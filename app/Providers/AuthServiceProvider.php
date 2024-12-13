<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;
class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
        'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
        $this->registerPolicies();

        /*You're creating a personal access token that belongs to user.
        A personal access token has a default expiration date of 1 year.
        Looking at your code I'm pretty sure that this command should do the work:
        */


        Passport::tokensExpireIn(now()->addDays(15));
        Passport::refreshTokensExpireIn(now()->addDays(30));
        //Passport::personalAccessClientId('client-id');

        Passport::tokensCan([
            'user_profile' => 'see user profile',
            'email-notify' => 'email notification after summission.',
            'list-acceptance' => 'list of accepted papers. ',
            // other
        ]);
    }
}
