<?php

namespace App\Http\Controllers\Users\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Service\DashboardService;

class WithDrawalController extends Controller
{

    protected $dashboardService;

    public function __construct(DashboardService $dashboardService) { $this->dashboardService = $dashboardService; }


    public function requestWithDrawal(Request $request) {
        return $this->dashboardService->withDrawal($request->all());
    }

}
