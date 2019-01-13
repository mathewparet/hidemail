<?php

namespace App\Http\Controllers;

use App\Email;
use Illuminate\Http\Request;
use App\Http\Requests\CreateHiddenEmailIdRequest;

class EmailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $emails = auth()->user()->emails();
        if($request->filled('filter')) $emails = $emails->like($request->query('filter'));
        $emails = $emails->orderBy('email','asc')->paginate(config('app.page'));

        return compact('emails');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateHiddenEmailIdRequest $request)
    {
        $this->authorize('store', Email::class);
        
        $email = $request->user()->addEmail(new Email(['email'=>$request->email]));

        return response(['message'=>__('Email ID successfully added'), 'email'=>$email]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Email  $email
     * @return \Illuminate\Http\Response
     */
    public function show(Email $email)
    {
        $this->authorize('show', $email);

        return compact('email');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Email  $email
     * @return \Illuminate\Http\Response
     */
    public function destroy(Email $email)
    {
        $this->authorize('destroy', $email);

        $email->delete();

        return response(['messsage'=>__('Succcessfully deleted :email', ['email'=>$email->hidden_email])]);
    }
}
