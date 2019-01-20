<?php

namespace App\Http\Controllers;

use App\SocialLogin;
use Illuminate\Http\Request;
use Socialite;
use Hash;
use DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Foundation\Auth\RedirectsUsers;
use Laravel\Socialite\Two\InvalidStateException;

use App\User;

class SocialLoginController extends Controller
{
    use RedirectsUsers;

    /**
     * Link a user to his social profile
     * 
     * @param Illuminate\Http\Request $request
     * @param \App\User $user
     * 
     * @returns Object - instance of the redirector
     */
    public function link(Request $request, User $user)
    {
        $this->authorize('link', $user);
        
        $user->addSocialLogin(new SocialLogin(['provider'=>$request->query('provider'), 'provider_id'=>$request->query('provider_id')]));
        return redirect('/profile');
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
     * Invoked when callback is received from social login provider
     * 
     * @param string $provider - the name of the provider
     */
    public function handleProviderCallback($provider)
    {
        $user = null;

        try
        {
            $social = Socialite::driver($provider)->user();

            $SPUser = SocialLogin::where([
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
        catch(InvalidStateException $e)
        {
            return redirect()->route('login')->with('loginMessage','Invalid state. Please try again!');
        }
        
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
            $user->addSocialLogin(new SocialLogin(['provider'=>$provider, 'provider_id'=>$social->getId()]));
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
                'login.socialite.link', 
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
}
