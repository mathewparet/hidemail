<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Illuminate\Http\Request;

use Socialite;
use App\User;
use App\SocialProviderUser;
use Hash;
use DB;
use Illuminate\Support\Facades\URL;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Override credentials() to check suspended field
     */
    public function credentials(Request $request) 
    {
        return ['email_hash' => sha1($request->email), 'password' => $request->password, 'suspended' => false];
    }

    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)
                        ->scopes(['email'])
                        ->redirect();
    }

    private function getUserFromResponse($response_user, $provider)
    {
        $user = null;

        $SPUser = SocialProviderUser::whereProviderId($response_user->getId())->first();
        
        if($SPUser)
            $user = $SPUser->user()->first();
        else
        {
            if(!$response_user->getEmail() || strlen($response_user->getEmail()) === 0)
                return redirect()->route('login')->withErrors(['general'=>["Email ID missing in response from ".$provider.'.']]);

            $user = User::whereEmailHash(sha1($response_user->getEmail()))->first();
            if($user)
            {
                $url = URL::temporarySignedRoute(
                    'social.link', 
                    now()->addHours(config('app.sign_expiry.short')),
                    [
                        'user'=>$user->id,
                        'provider'=>$provider, 
                        'email'=>$response_user->getEmail(),
                        'name'=>$response_user->getName(),
                        'provider_id'=>$response_user->getId(),
                    ]
                );
                request()->session()->put('_old_input.email', $response_user->getEmail());
                request()->session()->put('loginMessage', 'A user account with '.$response_user->getEmail().' already exists. Please login in order to associate your account with '.$provider.'.');
                return redirect($url);
            }

            $user = DB::transaction(function() use($user, $response_user, $provider){
                $user = User::create(['name'=>$response_user->getName(), 'email'=>$response_user->getEmail(), 'password'=>Hash::make(str_random(9))]);
                $user->markEmailAsVerified();
                $user->addProvider(new SocialProviderUser(['provider'=>$provider, 'provider_id'=>$response_user->getId()]));
                return $user;
            });
        }

        if(!$user->suspended)
        {
            auth()->login($user);
            return redirect()->intended($this->redirectPath());
        }
        return redirect()->route('login')->withErrors(['general'=>['Your account is suspended.']]);
    }

    public function handleProviderCallback($provider)
    {
        $user = Socialite::driver($provider)->user();

        return $this->getUserFromResponse($user, $provider);
    }

}
