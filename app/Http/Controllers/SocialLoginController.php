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
     * Return socilate config to JS object
     */
    public function config()
    {
        $social = config('services.social');
        return response(compact('social'));
    }

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
     * Get internal user based ot the response from Social Login provider
     * 
     * @param string $provider - name of the provider - E.g. facebook, google
     * @param object $social_login_response_user - response user object from the social login provider
     */
    private function getLocalUserFromSocialUser($provider, $social_login_response_user)
    {
        $SUser = SocialLogin::where([
            'provider'=>$provider, 
            'provider_id'=>$social_login_response_user->getId()
            ])
            ->first();
        
        return $SUser ? $SUser->user()->first() : null ;
    }

    /**
     * Check if user is already logged in, if yes attach to the user
     * 
     * @param string $provier - the name of the provider
     * @param $social_login_response_user  - response user object from the social login provider
     * 
     * @return mixed
     */
    private function attachLocalUserToSocialUserIfUserIsLoggdIn($provider, $social_login_response_user)
    {
        if(!request()->user())
            return false;
        
        request()->user()->addSocialLogin(new SocialLogin(['provider'=>$provider, 'provider_id'=>$social_login_response_user->getId()]));
        return redirect('/profile');
    }

    /**
     * Confirm whether the social login providers login response have an email id
     * 
     * @param object $social_login_response_user
     * 
     * @return mixed
     */
    private function validateEmailIdInSocialResponse($social_login_response_user)
    {
        if(!$social_login_response_user->getEmail() || strlen($social_login_response_user->getEmail()) === 0)
            return redirect()->route('login')->withErrors(['general'=>["Email ID missing in response from ".$provider.'.']]);
        else
            return false;
    }

    /**
     * Invoked when callback is received from social login provider
     * 
     * @param string $provider - the name of the provider
     */
    public function handleProviderCallback($provider)
    {
        try
        {
            $social_login_response_user = Socialite::driver($provider)->user();

            $user = $this->getLocalUserFromSocialUser($provider, $social_login_response_user);
            
            if(!$user) // user is not already in system / not mapped
            {
                // lets map the user to the current user if logged in
                $redirect = $this->attachLocalUserToSocialUserIfUserIsLoggdIn($provider, $social_login_response_user);
                if($redirect)
                    return $redirect;

                // nope, user is not logged in. Lets confirm that an email ID was sent along with the response
                $redirect = $this->validateEmailIdInSocialResponse($social_login_response_user);
                if($redirect)
                    return $redirect;

                // yes, we have an email id. Lets check whether there is any account in the system with that email
                $user = User::whereEmailHash(sha1($social_login_response_user->getEmail()))->first();
    
                if($user)
                {
                    $this->setSessionVariables($social_login_response_user, $provider);
                    return redirect($this->prepareSignedUrlToLinkExistingAccount($user, $provider, $social_login_response_user));
                }
    
                // nope - lets create a new user with the information received from the provider
                $user = $this->createUserFromSocialAccount($social_login_response_user, $provider);
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
            return redirect()->route('login')->with('loginMessage','Invalid state! Did you reload the page? Please try again!');
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
    private function createUserFromSocialAccount($social, $provider)
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

    public function destroy(Request $request, User $user, SocialLogin $socialLogin)
    {
        $this->authorize('deLink', $user);

        if(!$user->owns($socialLogin))
            abort(403, 'Unauthorized action');

        $socialLogin->delete();

        return response(['message' => 'Removed '.$socialLogin->provider.' link from your account.', 'user'=>$user->fresh()]);
    }
}
