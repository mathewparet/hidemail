<?php

namespace App\Http\Controllers;

use App\Email;
use Illuminate\Http\Request;
use App\Rules\IsHuman;

class GuestEmailController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'email'=>'required|email',
            'recaptcha'=>[
                'required',
                new IsHuman
            ]
        ]);

        $email = Email::create(['email' => $request->email]);

        return response(['message'=>'Email successfully hidden.', 'email'=>$email]);
    }

}
