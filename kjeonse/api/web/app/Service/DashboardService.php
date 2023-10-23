<?php

namespace App\Service;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardService {


    // [c] Dashboard -> Dashboard
    public function index($request) {

        $success = false;
        $msg     = '';
        $data    = [];

        try {

            $user_id = Auth::user()->id;

            $data['stock_info_section']     = \App\Models\UserAssets::userStockInfo($user_id);
            $data['jeonse_section']         = \App\Models\UserEstate::list($user_id);
            $data['stock_detail_section']   = \App\Models\UserStock::list($user_id);

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error : Dashboard Index Service ' . $e;
        }

        return response()->json([ 'success' => $success, 'msg' => $msg, 'data' => $data ]);

    }


    // [c] Dashboard -> Portfolio
    public function stockList($request) {

        $success = false;
        $msg     = '';
        $data    = [];

        try {

            $user_id = Auth::user()->id;

            $data['stockInfoSection'] = \App\Models\UserAssets::portfolio($user_id);
            $data['stockList']        = \App\Models\UserStock::userStockList($user_id);
            $data['dividendList']     = \App\Models\UserDividend::list($user_id);
            $data['stockTypeList']    = \App\Models\UserStock::stockTypeList($user_id);

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error : Dashboard StockList Service ' . $e;
        }

        return response()->json([ 'success' => $success, 'msg' => $msg, 'data' => $data ]);

    }


    // [c] 수익 리스트
    public function dividendList($request) {

        $success = false;
        $msg     = '';
        $data    = [];

        try {

            $user_id = Auth::user()->id;

            $data['dividendListSection'] = \App\Models\UserAssets::dividendSectionList($user_id);
            $data['dividendList'] = \App\Models\UserAssets::dividendList($user_id);
            $data['status'] = [ '0:charge', '1:stock', '2:deposit', '3:dividend'];

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error : Dashboard Dividend List Service ' . $e;
        }

        return response()->json([ 'success' => $success, 'msg' => $msg, 'data' => $data ]);

    }


    // [c] 투자 수익 리스트 검색
    public function stockDividendListSearch($request) {

        $success = false;
        $msg     = '';
        $data    = [];

        try {

            $user_id = Auth::user()->id;

            $data['dividendList'] = \App\Models\UserAssets::dividendListSearchResult($user_id, $request['start_date'], $request['end_date'], $request['type']);
            $data['status'] = [ '0:charge', '1:stock', '2:deposit', '3:dividend'];

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error : Dashboard Dividend List Service ' . $e;
        }

        return response()->json([ 'success' => $success, 'msg' => $msg, 'data' => $data ]);

    }


    // [c] 출금 요청
    public function withDrawal($request) {

        $success = false;
        $msg     = '';
        $data    = [];

        try {

            $user_id = Auth::user()->id;

            DB::beginTransaction();

            // 출금 가능 금액 유효성 체크
            if(\App\Models\UserAssets::getCanUseMyAmount($user_id) >= $request['stock_amount']) {

                // 출금 관련 정보 저장 처리
                $userBankInfo_id = \App\Models\UserBankInfo::store($user_id, $request['bank_id'] ,$request['bank_account_number']);

                // 출금 요청 테이블 Insert
                \App\Models\UserRequestWithDrawal::store($user_id, $userBankInfo_id, $request['stock_amount']);

                // 출금요청으로 인한 포인트 차감 처리
                \App\Models\UserAssets::setWithDrawAmount($user_id, $request['stock_amount']);

                DB::commit();

            } else {

                $msg = '출금 가능한 금액을 초과하여 처리하지 못했습니다.';

            }

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error : Dashboard User Request WithDrawal Service ' . $e;
            DB::rollBack();
        }

        return response()->json([ 'success' => $success, 'msg' => $msg, 'data' => $data ]);

    }


    // [c] 등록한 전세 count check
    public function estateZeroCheck($request) {

        $success = false;
        $msg     = '';
        $data    = [];

        try {

            $user_id = Auth::user()->id;

            $data['estate_count'] = \App\Models\UserEstate::check($user_id);

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error : estate Zero Check ' . $e;
        }

        return response()->json([ 'success' => $success, 'msg' => $msg, 'data' => $data ]);

    }


    // [c] 전세 부동산 관리 계약 관리 리스트
    public function estateContractorList($request) {

        $success = false;
        $msg     = '';
        $data    = [];

        try {

            $user_id = Auth::user()->id;

            $data['title'] = \App\Models\UserEstate::getEstateTitle($request['ref_estate_list_id']);
            $data['list'] =  \App\Models\UserEstateContractor::list($request['ref_estate_list_id'], $user_id);

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error : estate contractor list ' . $e;
        }

        return response()->json([ 'success' => $success, 'msg' => $msg, 'data' => $data ]);

    }


    // [c] 전세 부동산 계약 관리 상세보기
    public function estateContractorDetail($request) {

        $success = false;
        $msg     = '';
        $data    = [];

        try {

            $data = \App\Models\UserEstateContractor::detail($request['ref_estate_list_id']);

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error : estate contractor detail ' . $e;
        }

        return response()->json([ 'success' => $success, 'msg' => $msg, 'data' => $data ]);

    }


    // [c] 전세 부동산 관리 시설 관리 문의 리스트
    public function estateQuestionList($request) {

        $success = false;
        $msg     = '';
        $data    = [];

        try {

            $data['title'] = \App\Models\UserEstate::getEstateTitle($request['ref_estate_list_id']);
            $data['list'] = \App\Models\UserEstateQuestion::list($request['ref_estate_list_id'], Auth::user()->id);

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error : estate Question List '.$e;
        }

        return response()->json([ 'success' => $success, 'msg' => $msg, 'data' => $data ]);

    }


    // [c] 전세 부동산 관리 시설 관리 문의 상세보기
    public function estateQuestionDetail($request) {

        $success = false;
        $msg     = '';
        $data    = [];

        try {

            $data = \App\Models\UserEstateQuestion::detail($request['ref_estate_list_id']);

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error : estate Question Detail';
        }

        return response()->json([ 'success' => $success, 'msg' => $msg, 'data' => $data ]);

    }


    // [c] 전세 부동산 관리 시설 관리 문의 상세보기 임대인의 상태 변경
    public function estateQuestionDetailStatusChange($request) {

        $success = false;
        $msg     = '';
        $data    = [];

        try {

            \App\Models\UserEstateQuestion::updateStatus($request['category_id'], $request['ref_estate_list_id']);

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error : estate question detail category change ' . $e;
        }

        return response()->json([ 'success' => $success, 'msg' => $msg, 'data' => $data ]);

    }


    // [c] 전세 부동산 관리 관리자 공지 리스트
    public function estateAdminNoticeList($request) {

        $success = false;
        $msg     = '';
        $data    = [];

        try {

            $user_id = Auth::user()->id;

            $data['title'] = \App\Models\UserEstate::getEstateTitle($request['ref_estate_list_id']);
            $data['list'] = \App\Models\UserEstateAdminNotice::list($request['ref_estate_list_id'], $user_id);

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error : estate Admin Notice list';
        }

        return response()->json([ 'success' => $success, 'msg' => $msg, 'data' => $data ]);

    }


    // 전세 부동산 관리 관리자 공지 상세보기
    public function estateAdminNoticeDetail($request) {

        $success = false;
        $msg     = '';
        $data    = [];

        try {

            $data['detail'] = \App\Models\UserEstateAdminNotice::detail($request['id']);
            $data['files'] = \App\Models\UserEstateFiles::getFiles(2, $request['id']);

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error : estate Admin Notice list';
        }

        return response()->json([ 'success' => $success, 'msg' => $msg, 'data' => $data ]);

    }


    /* ---------------------------------------------------------------------------------------------------------------------------------------------- */
    // [c] 계약 문서 삭제
    public function deleteEstateContractor($request) {

        $success = false;
        $msg     = '';
        $data    = [];

        try {

            \App\Models\UserEstateContractor::remove($request['id']);
            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error : estate Contractor Delete ';
        }

        return response()->json([ 'success' => $success, 'msg' => $msg, 'data' => $data ]);

    }


    // [c] 시설 관리 문의 삭제
    public function deleteEstateQuestion($request) {

        $success = false;
        $msg     = '';
        $data    = [];

        try {

            \App\Models\UserEstateQuestion::remove($request['id']);
            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error : estate Question Delete ';
        }

        return response()->json([ 'success' => $success, 'msg' => $msg, 'data' => $data ]);

    }


    // [c] 계약 문서 등록
    public function registerEstateContractor($request) {

        $success = false;
        $msg     = '';
        $data    = [];

        try {

            $user_id = Auth::user()->id;
            $document_id = null;

            if($document_id=\App\Models\UserEstateContractor::register($request, $user_id)) {
                \App\Models\UserEstateFiles::user_contractor_upload_files($request, $document_id, 0);
            }

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error : estate Contractor Register '.$e;
        }

        return response()->json([ 'success' => $success, 'msg' => $msg, 'data' => $data ]);

    }


    // [c] 시설 관리 문서 등록
    public function registerEstateQuestion($request) {

        $success = false;
        $msg     = '';
        $data    = [];

        try {

            $user_id = Auth::user()->id;

            if(\App\Models\UserEstate::estatePolicyCheck($request['estate_id'], $user_id)) {

                $document_id = \App\Models\UserEstateQuestion::register($request, $user_id);
                \App\Models\UserEstateFiles::user_question_upload_files($request['files'], $document_id);

            } else {

                $msg = '잘못된 접근입니다.';

            }

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error : estate Question Register ';
        }

        return response()->json([ 'success' => $success, 'msg' => $msg, 'data' => $data ]);

    }


    // [c] 계약 관련 업데이트
    public function updateEstateContractor($request) {

        $success = false;
        $msg     = '';
        $data    = [];

        try {

            $user_id = Auth::user()->id;

            DB::beginTransaction();

            if(\App\Models\UserEstateContractor::documentPolicyCheck($request['id'], $user_id)) {

                if(!empty($request['files'])) {
                    \App\Models\UserEstateFiles::setUpdateFiles(0, $request['id'], $request['files']);
                }

                if(!empty($request['remove'])) {
                    \App\Models\UserEstateFiles::removeFiles(0, $request['remove']);
                }

                \App\Models\UserEstateContractor::setUpdate($request['id'], $request['title'], $request['content']);

            } else {
                $msg = '잘못된 접근입니다.';
            }

            DB::commit();

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error : estate Contractor Update '. $e;
            DB::rollBack();
        }

        return response()->json([ 'success' => $success, 'msg' => $msg, 'data' => $data ]);

    }


    //
    public function registerLessor($request) {

        $success = false;
        $msg     = '';
        $data    = [];

        try {

            $user_id = Auth::user()->id;

            DB::beginTransaction();

            if(\App\Models\UserEstate::registerLessor($user_id, $request)) {
                $success = true;
                DB::commit();
            } else {
                $msg = 'Error Register Lessor';
            }

        } catch (\Exception $e) {
            $msg = 'Error : estate Contractor Register '.$e;
            DB::rollBack();
        }

        return response()->json([ 'success' => $success, 'msg' => $msg, 'data' => $data ]);

    }


    // 임차인 전세 등록
    public function registerLessee($request) {

        $success = false;
        $msg     = '';
        $data    = [];

        try {

            $user_id = Auth::user()->id;

            DB::beginTransaction();

            if(\App\Models\UserEstate::registerLessee($user_id, $request)) {
                $success = true;
                DB::commit();
            } else {
                $msg = '전세 등록이 완료된 주소이거나, 패스워드 또는 선택된 주소가 틀려 등록을 완료할 수 없습니다.';
            }

        } catch (\Exception $e) {
            $msg = 'Error : estate Contractor Register '.$e;
            DB::rollBack();
        }

        return response()->json([ 'success' => $success, 'msg' => $msg, 'data' => $data ]);

    }


    //
    public function searchEstateAddresss($request) {

        $success = false;
        $msg     = '';
        $data    = [];

        try {

            $data = \App\Models\UserEstate::search($request['address']);
            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error : estate search'.$e;
        }

        return response()->json([ 'success' => $success, 'msg' => $msg, 'data' => $data ]);

    }


    // 해당 Function 기능서 내 의도적으로 포함되지 않은 것으로 사용 제외됩니다.
    public function updateEstateQuestion($request) {

        $success = false;
        $msg     = '';
        $data    = [];

        try {

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error : estate Question Update ';
        }

        return response()->json([ 'success' => $success, 'msg' => $msg, 'data' => $data ]);

    }


    //
    public function stock($request) {

        $success = false;
        $msg     = '';
        $data    = [];

        try {

            $user_id = Auth::user()->id;

            DB::beginTransaction();

            // 투자 시점 해당 공모전의 토큰 가격
            $tokenAmount = (int) \App\Models\Contest::getContestTokenAmount($request['contest_id'])->token_amount;

            // 투자 시점 유저가 투자한 토큰 개수
            $user_buy_token_count = (int)$request['buy_token_count'];

            // 투자 시점 유저가 투자한 금액
            $user_buy_amount = (int)$request['amount'];

            $data['1'] = $tokenAmount;
            $data['2'] = $user_buy_token_count;
            $data['3'] = $user_buy_amount;

            // 투자 시점 기준, 사용자로부터 전달 받은 데이터가 유효한지 체크한다. [ 전체 투자 금액 / 공모전 토큰 개당 가격 == 전체 투자 금액 비례 토큰 개수 ]
            if($user_buy_amount / $tokenAmount == $user_buy_token_count) {

                // 1. 투자 시점 기준, 공모전 투자 진행 현황 대비 사용자가 투자한 금액으로 인해 초과되는지 체크한다.
                $checkContestMaxStockAmountOver = \App\Models\Contest::stockOverCheck($request['contest_id'], $user_buy_token_count, $user_buy_amount);

                if(!$checkContestMaxStockAmountOver) {
                    $msg = '비정상적인 투자 진행이 감지되었습니다. [1]';
                    return response()->json([ 'success' => $success, 'msg' => $msg, 'data' => $data ]);
                }

                // 2. 투자 시점 및 투자 방법 기준 : 예치금 사용 시, 보유하고 있는 예치금 내에서 투자 진행 가능한지 체크한다.
                $user_assets_over_check = false;

                if($request['type'] == 0
                    && \App\Models\UserAssets::stockOverCheck($user_id, $user_buy_amount))
                {
                    $user_assets_over_check = true;
                }

                // 3. 투자 시점 및 투자 방법 기준 : 전세 자금 사용 시, 해당 전세 자금 내에서 투자 진행 가능한지 체크한다.
                if($request['type'] == 1
                    && \App\Models\UserEstate::stockOverCheck($user_id, $user_buy_amount, $request['estate_id']))
                {
                    $user_assets_over_check = true;
                }

                if(!$user_assets_over_check) {
                    $msg = '심각. 비정상적인 투자 진행이 감지되었습니다. [Over]';
                    return response()->json([ 'success' => $success, 'msg' => $msg, 'data' => $data ]);
                }

                // 투자가 정상적으로 진행 완료될 경우 모든 데이터를 저장한다.
                if(\App\Models\UserStock::stocked($user_id, $tokenAmount, $request))
                {
                    // 투자 완료 이후, 회원 자산 정보 업데이트 및 공모전 현재 투자 금액 업데이트
                    \App\Models\UserAssets::minusStockAssests($user_id, $request['type'], $user_buy_amount);
                    \App\Models\Contest::userStockedUpdateContestCost($request['contest_id'], $user_buy_amount);

                    $success = true;
                    DB::commit();
                } else {
                    $msg = 'Error Stock Not Working Save Data';
                    DB::rollBack();
                }

            } else {
                $msg = "심각. 비정상적인 투자 진행이 감지되었습니다 [2]";
                // 계정 잠금 및 시스템 관리자 및 사용자에게 긴급 알림
            }

        } catch (\Exception $e) {
            $msg = 'Error : Stock : ' . $e;
        }

        return response()->json([ 'success' => $success, 'msg' => $msg, 'data' => $data ]);

    }


    //
    public function alaramList($request) {

        $success = false;
        $msg     = '';
        $data    = [];

        try {

            $user_id = Auth::user()->id;

            $data = \App\Models\Notification::list($user_id);
            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error : User alaram List ' .$e;
        }

        return response()->json([ 'success' => $success, 'msg' => $msg, 'data' => $data ]);

    }


    //
    public function alarmCheck($request) {

        $success = false;
        $msg     = '';
        $data    = [];

        try {

            $user_id = Auth::user()->id;

            $isHaveNonReadAlaram = \App\Models\Notification::nonReadAlaramList($user_id);
            $data = $isHaveNonReadAlaram > 0;
            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error : User alaram Check ' .$e;
        }

        return response()->json([ 'success' => $success, 'msg' => $msg, 'data' => $data ]);

    }

    /* ---------------------------------------------------------------------------------------------------------------------------------------------- */

    // [c]  전세 부동산 관리 시설 관리 문의 카테고리
    public function estateQuestionCategory() {

        $success = false;
        $msg     = '';
        $data    = [];

        try {

            $data = \App\Models\UserEstateQuestionCategory::list();
            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error : estate Question Update ';
        }

        return response()->json([ 'success' => $success, 'msg' => $msg, 'data' => $data ]);

    }


    /* ------------------------------------------------------------------ */

}
