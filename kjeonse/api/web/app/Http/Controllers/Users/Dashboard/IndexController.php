<?php

namespace App\Http\Controllers\Users\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Service\DashboardService;

class IndexController extends Controller
{

    protected $dashboardService;

    public function __construct(DashboardService $dashboardService) { $this->dashboardService = $dashboardService; }


    /*
     * @Paramter    User_id
     * @return      User Stock & Jeonse Info
    */
    public function index(Request $request) {
        return $this->dashboardService->index($request->all());
    }


    //
    public function stockList(Request $request) {
        return $this->dashboardService->stockList($request->all());
    }


    // 유저 수익관리 리스트
    public function dividendList(Request $request) {
        return $this->dashboardService->dividendList($request->all());
    }
    
}
