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

    /**
     * Redirect user to Socialite Provider's endpoint
     * 
     * @param string $provider
     * 
     * @returns redirection to provider endpoint
     */
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)
                        ->scopes(['email'])
                        ->redirect();
    }

    /**
     * Create a local user account from social account
     * 
     * @param Object $social - object representing response from the provider
     * @param String $provider - E.g. facebook or google
     * 
     * @return \App\User $user
     */
    private function createUserFromSocialAccount($social, $provier)
    {
        return DB::transaction(function() use($social, $provider){
            $user = User::create(['name'=>$social->getName(), 'email'=>$social->getEmail(), 'password'=>Hash::make(str_random(9))]);
            $user->markEmailAsVerified();
            $user->addProvider(new SocialProviderUser(['provider'=>$provider, 'provider_id'=>$social->getId()]));
            return $user;
        });
    }

    /**
     * Set required session variables
     * 1. _old_input.email - the value to pre-fill the login email
     * 2. loginMessage - a message to be shown to the user at login screen
     * 
     * @param String $social - object representing response form social login provider
     * @param String $provider - the social login provider key. E.g. facebook, google
     * 
     * @return void
     */
    private function setSessionVariables($social, $provider)
    {
        request()->session()->put('_old_input.email', $social->getEmail());
        request()->session()->put('loginMessage', 'A user account with '.$social->getEmail().' already exists. Please login in order to associate your account with '.$provider.'.');
    }

    /**
     * Get signed url to link existing account to provider account
     * 
     * @param \App\User $user
     * @param String $provider - the provider key. E.g. facebook or google
     * @param Object $social - object representing response from provider
     * 
     * @returns String $url
     */
    private function prepareSignedUrlToLinkExistingAccount(User $user, $provider, $social)
    {
        return URL::temporarySignedRoute(
                'social.link', 
                now()->addHours(config('app.sign_expiry.short')),
                [
                    'user'=>$user->id,
                    'provider'=>$provider, 
                    'email'=>$social->getEmail(),
                    'name'=>$social->getName(),
                    'provider_id'=>$social->getId(),
                ]
            );
    }

    public function handleProviderCallback($provider)
    {
        $user = null;
        
        $social = Socialite::driver($provider)->user();
        
        $SPUser = SocialProviderUser::where([
            'provider'=>$provider, 
            'provider_id'=>$social->getId()
            ])
            ->first();
        
        if($SPUser)
            $user = $SPUser->user()->first();
        else
        {
            if(!$social->getEmail() || strlen($social->getEmail()) === 0)
                return redirect()->route('login')->withErrors(['general'=>["Email ID missing in response from ".$provider.'.']]);

            $user = User::whereEmailHash(sha1($social->getEmail()))->first();

            if($user)
            {
                $this->setSessionVariables($social, $provider);
                return redirect($this->prepareSignedUrlToLinkExistingAccount($user, $provider, $social));
            }

            $user = $this->createUserFromSocialAccount($social, $provider);
        }

        if(!$user->suspended)
        {
            auth()->login($user);
            return redirect()->intended($this->redirectPath());
        }
        return redirect()->route('login')->withErrors(['general'=>['Your account is suspended.']]);
    }

}
