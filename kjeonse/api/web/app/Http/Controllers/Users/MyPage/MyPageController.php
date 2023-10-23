<?php
// Real

namespace App\Http\Controllers\Users\MyPage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Service\UserService;
use App\Service\DashboardService;

class MyPageController extends Controller
{

    protected $userService;
    protected $dashboardService;

    public function __construct(UserService $userService, DashboardService $dashboardService) {
        $this->userService = $userService;
        $this->dashboardService = $dashboardService;
    }

    /* ---------------------------------------------------------------------------------------------------------------------------------------------- */

    // User MyPage
    public function mypage(Request $request) { return $this->userService->mypage($request->all()); }


    // User Password Chage
    public function chagePassword(Request $request) { return $this->userService->chagePassword($request->all()); }


    // User info Update
    public function infoUpdate(Request $request) { return $this->userService->userInfoUpdate($request->all()); }


    // User Leave
    public function userLeave(Request $request) { return $this->userService->userLeave($request->all()); }


    // User Portfolio
    public function stockList(Request $request) { return $this->userService->stockList($request->all()); }


    // User Contest List
    public function list(Request $request) { return $this->userService->contestList($request->all()); }


    // 유저 수익관리 리스트 검색
    public function stockListSearch(Request $request) { return $this->dashboardService->stockDividendListSearch($request->all()); }

    /* ---------------------------------------------------------------------------------------------------------------------------------------------- */

    // 전세 현황 리스트
    public function estateList(Request $request) { return $this->userService->estateList($request->all()); }


    // 메뉴 '전세부동산 관리' 클릭 시, 등록된 전세가 없는 경우 등록 페이지 오픈
    public function estateCountZeroCheck(Request $request) { return $this->dashboardService->estateZeroCheck($request->all()); }


    // 메뉴 '전세부동산 관리' 임대차 계약 관리 리스트
    public function estateContractorList(Request $request) { return $this->dashboardService->estateContractorList($request->all()); }


    // 메뉴 '전세부동산 관리' 임대차 계약 관리 상세보기
    public function estateContractorDetail(Request $request) { return $this->dashboardService->estateContractorDetail($request->all()); }


    // 메뉴 '전세부동산 관리' 시설 관리/문의 리스트
    public function estateQuestion(Request $request) { return $this->dashboardService->estateQuestionList($request->all());}


    // 메뉴 '전세부동산 관리' 시설 관리/문의 상세보기
    public function estateQuestionDetail(Request $request) { return $this->dashboardService->estateQuestionDetail($request->all()); }


    // 메뉴 '전세부동산 관리' 시설 관리/문의 상세보기 임대인의 상태 변경
    public function questionStatueChange(Request $request) { return $this->dashboardService->estateQuestionDetailStatusChange($request->all()); }


    // 메뉴 '전세부동산 관리' 관리자 공지 리스트
    public function estateAdminNotice(Request $request) { return $this->dashboardService->estateAdminNoticeList($request->all()); }


    // 메뉴 '전세부동산 관리' 관리자 공지 상세보기
    public function estateAdminNoticeDetail(Request $request) { return $this->dashboardService->estateAdminNoticeDetail($request->all()); }


    /* ---------------------------------------------------------------------------------------------------------------------------------------------- */

    // 전세 관리 계약 관련 삭제
    public function deleteEstateContractor(Request $request) { return $this->dashboardService->deleteEstateContractor($request->all()); }


    // 전세 관리 시설 관리문의 관련 삭제
    public function deleteEstateQuestion(Request $request) { return $this->dashboardService->deleteEstateQuestion($request->all()); }


    // 전세 관리 계약 관련 수정
    public function updateEstateContractor(Request $request) { return $this->dashboardService->updateEstateContractor($request->all()); }


    // 전세 관리 시설 관리문의 관련 수정 : X 사용하지 않습니다.
    public function updateEstateQuestion(Request $request) { /* return $this->dashboardService->updateEstateQuestion($request->all()); */ }


    // 전세 관리 계약 관련 등록
    public function registerEstateContractor(Request $request) { return $this->dashboardService->registerEstateContractor($request->all()); }


    // 전세 관리 시설 관리문의 관련 등록
    public function registerEstateQuestion(Request $request) { return $this->dashboardService->registerEstateQuestion($request->all()); }


    // 임대인 전세 등록
    public function registerLessor(Request $request) { return $this->dashboardService->registerLessor($request->all()); }


    //
    public function registerLessee(Request $request) { return $this->dashboardService->registerLessee($request->all()); }


    //
    public function estateSearch(Request $request) { return $this->dashboardService->searchEstateAddresss($request->all()); }


    //
    public function alarmList(Request $request) { return $this->dashboardService->alaramList($request->all()); }


    //
    public function alarmCheck(Request $request) { return $this->dashboardService->alarmCheck($request->all()); }


    //
    public function findPassword(Request $request) { return $this->userService->findPassword($request->all()); }


    //
    public function findPasswordCertificate(Request $request) { return $this->userService->findPasswordCertificate($request->all()); }


    //
    public function findPasswordCertificateCheck(Request $request) { return $this->userService->findPasswordCertificateCheck($request->all()); }


    //
    public function findPasswordUpdate(Request $request) { return $this->userService->findPasswordUpdate($request->all()); }


    //
    public function userPhoneUpdateCertificate(Request $request) { return $this->userService->userPhoneUpdateCertificate($request->all()); }


    //
    public function userPhoneUpdateCertificateCheck(Request $request) { return $this->userService->userPhoneUpdateCertificateCheck($request->all()); }


    //
    public function findAccountSendingSmsAuthCode(Request $request) { return $this->userService->findAccountSendingSmsAuthCode($request->all()); }


    //
    public function findAccountSendingSmsAuthCodeCheck(Request $request) { return $this->userService->findAccountSendingSmsAuthCodeCheck($request->all()); }

    /* ---------------------------------------------------------------------------------------------------------------------------------------------- */

    // 메뉴 '전세부동산 관리' 시설 관리/문의 카테고리
    public function category_question() { return $this->dashboardService->estateQuestionCategory(); }


    //
    public function import() { return view('import/index'); }
}
