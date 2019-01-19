<?php

namespace App\Http\Controllers;

use App\SocialProviderUser;
use Illuminate\Http\Request;

use App\User;

class SocialProviderUserController extends Controller
{
    public function link(Request $request, User $user)
    {
        $this->authorize('link', $user);
        
        $user->addProvider(new SocialProviderUser(['provider'=>$request->query('provider'), 'provider_id'=>$request->query('provider_id')]));
        return redirect('/profile');
    }
}
