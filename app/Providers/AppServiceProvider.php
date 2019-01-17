<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\User;
use App\Email;
use View;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        try
        {
            $owner = null;
            $stats = null;
            if(optional(User::find(1))->emails)
            {
                $owner = cache()->rememberForever('owner', function() {
                    return Email::like('mathewparet@gmail.com')->where('user_id',1)->first();
                });

                $stats = cache()->remember('stats', 10, function(){
                    $num_emails_in_db = Email::count();
                    return $num_emails_in_db > 10293 ? $num_emails_in_db : 10293;
                });
            }
            View::share('owner', $owner);
            View::share('stats', number_format($stats));
        }
        catch(\Exception $e)
        {
            View::share('owner', new Email());
            View::share('stats',10293);
        }
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
