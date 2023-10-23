<?php
// Real

namespace App\Http\Controllers\Users\Stock;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Service\UserService;
use App\Service\DashboardService;

class StockController extends Controller
{

    protected $userService;
    protected $dashboardService;

    public function __construct(UserService $userService, DashboardService $dashboardService) {
        $this->userService = $userService;
        $this->dashboardService = $dashboardService;
    }

    /* ---------------------------------------------------------------------------------------------------------------------------------------------- */
    public function stock(Request $request) {
        return $this->dashboardService->stock($request->all());
    }


    public function stockMyInfo(Request $request) {
        return $this->userService->stockMyInfo($request->all());
    }

    /* ---------------------------------------------------------------------------------------------------------------------------------------------- */


}
