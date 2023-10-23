<?php
// Real

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Service\UserService;

class LoginController extends Controller
{

    protected $userService;

    public function __construct(UserService $userService) {
        $this->userService = $userService;
    }

    // Admin Login
    public function login(Request $request){
        return $this->userService->Login($request->all());
    }

    //
    public function mailing(Request $request) {
        return $this->userService->mailing($request->all());
    }

}
