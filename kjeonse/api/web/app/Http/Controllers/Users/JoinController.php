<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Service\UserService;


class JoinController extends Controller
{

    protected $userService;

    //
    public function __construct(UserService $userService) { $this->userService = $userService; }


    //
    public function join(Request $request) { return $this->userService->join($request->all()); }


    //
    public function info(Request $request) { return $this->userService->addInfo($request->all()); }


    //
    public function sms(Request $request) { return $this->userService->sms($request->all()); }


    //
    public function sms_check(Request $request) {
        return $this->userService->checkSms($request->all());
    }
}
