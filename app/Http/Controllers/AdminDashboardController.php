<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Email;

use DB;

class AdminDashboardController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        if($request->user()->id!==1)
            abort(403, 'Unauthorized');

        $num_users = User::count();
        
        $num_apps = collect(DB::select(
            DB::raw("select count(*) num_apps from oauth_clients where revoked = 0")
        ))->first()->num_apps;

        $num_api_keys = collect(DB::select(
            DB::raw("select count(*) num from oauth_access_tokens where revoked = 0")
        ))->first()->num;

        $num_emails = Email::count();
        
        return response(compact('num_users', 'num_apps', 'num_api_keys', 'num_emails'));
    }
}
