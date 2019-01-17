<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Rules\HashUnique;
use App\Rules\IsCurrentUser;
use Illuminate\Support\Facades\Hash;

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

        $user->name = $request->name;
        $user->email = $request->email;
        if($request->filled('password'))
            $user->password = Hash::make($request->password);
        $user->save();

        return response(['message'=>'Profile updated successfully','user'=>$user->fresh()]);
    }
}
