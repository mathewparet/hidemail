<?php

namespace App\Http\Controllers;

use App\Email;
use Illuminate\Http\Request;
use App\Rules\IsHuman;


class RevealEmailController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  \App\Email  $email
     * @return \Illuminate\Http\Response
     */
    public function show(Email $email)
    {
        return view('emails.reveal', compact('email'));
    }

    public function reveal(Email $email, Request $request)
    {
        $request->validate([
            'g-recaptcha-response' => [new IsHuman]
        ]);

        return response(['email'=>$email->email]);
    }
}
