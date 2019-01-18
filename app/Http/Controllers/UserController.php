<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Rules\HashUnique;
use App\Rules\IsCurrentUser;
use Illuminate\Support\Facades\Hash;

use App\Notifications\VerifyNewEmailId;
use App\Notifications\NewLoginEmailId;

class UserController extends Controller
{
    /**
     * Retrieve the user details from the request and send it back
     * 
     * @param Illuminate\Http\Request $request
     * 
     * @return App\User
     */
    public function info(Request $request)
    {
        return $request->user();
    }

    /**
     * If password is updated, then update the password field with the new hashed password.
     * Else leave it untouched
     * 
     * @param Illuminate\Http\Request $request
     * @param App\User $user
     * 
     * @return App\User
     */
    private function hashPasswordIfChanged(Request $request, User $user)
    {
        if($request->filled('password'))
            $user->password = Hash::make($request->password);

        return $user;
    }

    /**
     * Check if email has been updated. If yes, send a notification to the new email address to verify it
     * Else do nothing
     * 
     * @param Illuminate\Http\Request $request
     * @param App\User $user
     * 
     * @return string
     */
    private function sendEmailNotificationToNewEmailIfChanged(Request $request, User $user)
    {
        $message ='';

        $small_email = strtolower($request->email);

        if($small_email != $user->email)
        {
            $user->notify(new VerifyNewEmailId($small_email));
            $message = 'You will receive a verification link in your new email. You will need to click the link in the mail to update the email ID in your profile.';
        }

        return $message;
    }

    /**
     * Update the user's profile
     * 
     * @param Illuminate\Http\Request $request
     * @param App\User $user
     * 
     * @return mixed
     */
    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $request->validate([
            'name' => 'required|string|max:255',
            'email'=>['required', 'string', 'email', 'max:255', new HashUnique('users','email','id',$user->id)],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'current_password' => ['required', 'string', new IsCurrentUser]
        ]);

        $message = 'Profile updated successfully.';

        $user->name = $request->name;

        $user = $this->hashPasswordIfChanged($request, $user);
        
        $message .= $this->sendEmailNotificationToNewEmailIfChanged($request, $user);
        
        $user->save();

        return response(['message'=>$message,'user'=>$user->fresh()]);
    }

    /**
     * Update the user's mail ID in the database. This method is called after the verification link signature is validated to be correct
     * 
     * @param Illuminate\Http\Request $request
     * @param App\User $user
     * 
     * @return mixed
     */
    public function changeEmail(Request $request, User $user)
    {
        $this->authorize('changeEmail', $user);
        
        $user->email = $request->email;
        $user->save();
        $user->notify(new NewLoginEmailId());
        return redirect('/profile');
    }

    /** Get list of users registered in the system
     * 
     * @param Illuminate\Http\Request $request
     * 
     * @return mixed
     */
    public function index(Request $request)
    {
        $this->authorize('index', App\User::class);

        $users = $request->filled('filter') ? User::like($request->query('filter')) : new User();
        $users = $users->orderBy('created_at','desc')->paginate(config('app.page_size'));
        
        return compact('users');
    }

    /**
     * Toggle suspension of a user account
     * 
     * @param Illuminate\Http\Request $request
     * @param App\User $user
     * 
     * @return mixed
     */
    public function toggleSuspension(Request $request, User $user)
    {
        $this->authorize('toggleSuspension', $user);

        $user->suspended = !$user->suspended;
        $user->save();

        return response(['message'=>__(':user is now suspneded', ['user'=>$user->email]), 'user'=>$user->fresh()]);
    }

    public function show(Request $request, User $user)
    {
        $this->authorize('show', $user);

        return response(compact('user'));
    }
}
