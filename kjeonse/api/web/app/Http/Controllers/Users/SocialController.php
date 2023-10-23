<?php
// Real

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Popup;
use Illuminate\Http\Request;

use Socialite;

class SocialController extends Controller
{

    public function login($provider, Request $request) {

        $data = [];
        $msg = "";
        $success = false;

        try {

            $data = \App\Models\User::snsLogin($request->all(), $provider);
            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error.'.$e;
        }

        return response()->json(['success' => $success, 'msg' => $msg, 'data' => $data]);
    }


    public function register($provider, Request $request) {

        $data = [];
        $msg = "";
        $success = false;

        try {

            $data = \App\Models\User::register_sns($request->all(), $provider);
            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error.'.$e;
        }

        return response()->json(['success' => $success, 'msg' => $msg, 'data' => $data]);

    }
}
