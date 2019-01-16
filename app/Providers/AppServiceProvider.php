<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\User;
use App\Email;
use View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if(optional(User::find(1))->emails)
        {
            $owner = cache()->rememberForever('owner', function() {
                return Email::like('mathewparet@gmail.com')->where('user_id',1)->first();
            });
        }
        View::share('owner', $owner);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
