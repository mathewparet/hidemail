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
    public function info(Request $request)
    {
        return $request->user();
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email'=>['required', 'string', 'email', 'max:255', new HashUnique('users','email','id',$user->id)],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'current_password' => ['required', 'string', new IsCurrentUser]
        ]);

        $message = 'Profile updated successfully.';

        $user->name = $request->name;

        if($request->filled('password'))
            $user->password = Hash::make($request->password);
        
        $small_email = strtolower($request->email);
        if($small_email != $user->email)
        {
            $user->notify(new VerifyNewEmailId($small_email));
            $message .= ' You will receive a verification link in your new email. You will need to click the link in the mail to update the email ID in your profile.';
        }
        
        $user->save();

        return response(['message'=>$message,'user'=>$user->fresh()]);
    }

    public function changeEmail(Request $request, User $user)
    {
        $this->authorize('changeEmail', $user);
        
        $user->email = $request->email;
        $user->save();
        $user->notify(new NewLoginEmailId());
        return redirect('/profile');
    }
}
