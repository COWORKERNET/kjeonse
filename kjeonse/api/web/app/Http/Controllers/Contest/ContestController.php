<?php

namespace App\Http\Controllers\Contest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Service\ContestService;

class ContestController extends Controller
{

    protected $contestService;

    public function __construct(ContestService $contestService) { $this->contestService = $contestService; }


    /*
     * @Paramter    null
     * @return      null
    */
    public function like(Request $request) {
        return $this->contestService->like($request->all());
    }


    //
    public function likeList(Request $request) {
        return $this->contestService->likeList($request->all());
    }

}
