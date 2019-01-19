<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Illuminate\Http\Request;

use Socialite;
use App\User;
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

    private function getUserFromResponse($response_user)
    {
        if(!$response_user->getEmail() || strlen($response_user->getEmail()) === 0)
        {
            return redirect()->route('login')->withErrors(['email'=>["Email ID missing in response."]]);
        }

        $user = User::where('email_hash', 'like', sha1($response_user->getEmail()))->first();

        if(!$user)
        {
            $user = User::create(['name'=>$response_user->getName(), 'email'=>$response_user->getEmail(), 'password'=>Hash::make(str_random(9))]);
        }

        if(!$user->suspended)
        {
            auth()->login($user);
            return redirect()->intended($this->redirectPath());
        }
        return redirect()->route('login')->withErrors(['email'=>['Your account is suspended.']]);
    }

    public function handleProviderCallback($provider)
    {
        $fb_user = Socialite::driver($provider)->user();

        return $this->getUserFromResponse($fb_user);
    }

}
