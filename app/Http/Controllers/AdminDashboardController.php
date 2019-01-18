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
        
        $num_apps = $this->getNumApps();

        $num_api_keys = $this->getNumApiKeys();

        $num_emails = Email::count();
        
        return response(compact('num_users', 'num_apps', 'num_api_keys', 'num_emails'));
    }

    private function getNumApps()
    {
        $num_apps =  $this->getCount('oauth_clients');
        $num_apps -= 2; // 2 of these will be internal apps

        return $num_apps;
    }

    private function getNumApiKeys()
    {
        return $this->getCount('oauth_access_tokens');
    }

    private function getCount($table)
    {
        return collect(DB::select(
            DB::raw(__("select count(*) total_count from :table where revoked = 0", ['table'=>$table]))
        ))->first()->total_count;
    }
}
