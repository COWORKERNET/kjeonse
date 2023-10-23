<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Guid\Guid;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\About;
use App\Models\Associate;
use App\Models\AssociateFiles;
use App\Models\Bank;
use App\Models\Banner;
use App\Models\Popup;
use App\Models\BannerFiles;
use App\Models\BoardFiles;
use App\Models\CategoryBoard;
use App\Models\CertificateSms;
use App\Models\Contest;
use App\Models\ContestInfo;
use App\Models\ContestFiles;
use App\Models\ContestLikeHistory;
use App\Models\Faq;
use App\Models\Guide;
use App\Models\LeavedReason;
use App\Models\LeavedUser;
use App\Models\Notice;
use App\Models\Notification;
use App\Models\SystemDividendTime;
use App\Models\User;
use App\Models\UserAssets;
use App\Models\UserBankInfo;
use App\Models\UserDividend;
use App\Models\UserEstate;
use App\Models\UserEstateAdminNotice;
use App\Models\UserEstateContractor;
use App\Models\UserEstateFiles;
use App\Models\UserEstateQuestion;
use App\Models\UserEstateQuestionCategory;
use App\Models\UserJoinSurvey;
use App\Models\UserLessee;
use App\Models\UserLessor;
use App\Models\UserRequestWithDrawal;
use App\Models\UserStock;
use App\Models\Survey;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{

    // Template function
    public function common(Request $request) {

        $success = false;
        $msg = '';
        $data = [];

        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if($validator->fails()) {
            $msg = "[id],지정된 데이터를 모두 입력하세요.";
            return $this->jsonReturnResponse($success, $msg, $data);
        }

        try {

            DB::beginTransaction();
            $id = $request->input('id');

            if (Contest::admin_delete_contest($id)) {
                DB::commit();
                $success = true;
            }


        } catch (\Exception $e) {
            $msg = 'Error. ';
            DB::rollBack();
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }


    /* ---------------------------------------------------------------------------------- */
    // Admin list
    /* ---------------------------------------------------------------------------------- */
    // [c]
    public function list_popup() {

        $success = false;
        $msg = '';
        $data = [];

        try {

            $data['list'] = Popup::list();
            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error. ';
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // [c]
    public function list_banner() {

        $success = false;
        $msg = '';
        $data = [];

        try {

            $data['list'] = Banner::list();
            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error. ';
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // [c]
    public function list_associate() {

        $success = false;
        $msg = '';
        $data = [];

        try {

            $data['list'] = Associate::list();
            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error. ';
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // [c]
    public function list_introduce() {

        $success = false;
        $msg = '';
        $data = [];

        try {

            $data = About::admin_list();
            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error. ';
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // [c]
    public function list_guide() {

        $success = false;
        $msg = '';
        $data = [];

        try {

            $data['list'] = Guide::admin_list();
            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error. ' . $e;
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // [c]
    public function list_notice() {

        $success = false;
        $msg = '';
        $data = [];

        try {

            $data['list'] = Notice::admin_list();
            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error. ';
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // [c]
    public function list_faq() {

        $success = false;
        $msg = '';
        $data = [];

        try {

            $data['list'] = Faq::admin_list();
            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error. ' . $e;
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // [c]
    public function list_user() {

        $success = false;
        $msg = '';
        $data = [];

        try {

            $data['list'] = User::admin_list();
            $data['survey_1'] = Survey::list(1);
            $data['survey_2'] = Survey::list(2);
            $data['survey_3'] = Survey::list(3);
            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error. ' . $e;
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // [c]
    public function list_leaved_user() {

        $success = false;
        $msg = '';
        $data = [];

        try {

            $data['list'] = User::admin_leaved_list();
            $data['survey'] = LeavedUser::admin_leaved_list();

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error. ' . $e;
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // [c]
    public function list_contest() {

        $success = false;
        $msg = '';
        $data = [];

        try {

            $data['list'] = Contest::admin_list();
            $data['code'] = [
                '1 : 공모예정',
                '2 : 공모중',
                '3 : 공모마감',
            ];

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error. ';
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // [c]
    public function list_dividend() {

        $success = false;
        $msg = '';
        $data = [];

        try {

            $data['list'] = Contest::admin_dividend_list();
            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error. ' .$e;
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // [c]
    public function list_estate() {

        $success = false;
        $msg = '';
        $data = [];

        try {

            $data['list'] = UserEstate::admin_list();
            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error. ';
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // [..ing]
    public function list_assets() {

        $success = false;
        $msg = '';
        $data = [];

        try {

            $data['user_list'] = User::admin_user_list();

            // 전세 투자금
            $data['user_total_sum_stock_estate'] = UserStock::admin_getUserEstateAmount();

            // 전세 예치금
            $data['user_total_have_estate_amount'] = UserEstate::admin_getUserEstateSumAmount();

            // 일반 투자금
            $data['user_total_sum_stock_amount'] = UserStock::admin_getUserAmount();

            // 일반 예치금
            $data['user_total_have_amount'] = UserAssets::admin_getUserTotalHaveAmount();

            // 총 수익금
            $data['user_total_sum_dividend'] = UserDividend::admin_getUserDividendSumAmount();

            $data['guide'] = '총 보유자산의 경우 전세, 일반 투자 및 예치금 데이터를 활용하여 합산합니다.';

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error. ' . $e;
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // [c]
    public function list_withdraw() {

        $success = false;
        $msg = '';
        $data = [];

        try {

            $data['list'] = UserRequestWithDrawal::admin_list();

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error. '.$e;
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // [c]
    public function list_deposit() {

        $success = false;
        $msg = '';
        $data = [];

        try {

            $data['list'] = UserAssets::admin_deposit_list();
            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error. ' . $e;
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    /* ---------------------------------------------------------------------------------- */
    // [c]
    public function register_popup(Request $request) {

        $success = false;
        $msg = '';
        $data = [];

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'url' => 'required',
            'file' => 'required',
        ]);

        if($validator->fails()) {
            $msg = "[title, url, file],지정된 데이터를 모두 입력하세요.";
            return $this->jsonReturnResponse($success, $msg, $data);
        }

        try {

            DB::beginTransaction();

            if(Popup::create($request->all())) { DB::commit(); }

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error. ';
            DB::rollBack();
        }

        return $this->jsonReturnResponse($success, $msg, $data);
    }

    // [c]
    public function register_banner(Request $request) {
        $success = false;
        $msg = '';
        $data = [];

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'file' => 'required',
        ]);

        if($validator->fails()) {
            $msg = "[title, description, file], 지정된 데이터를 모두 입력하세요.";
            return $this->jsonReturnResponse($success, $msg, $data);
        }

        try {

            DB::beginTransaction();

            $admin_id = Auth::user()->id;

            if(Banner::create($request->all(), $admin_id)) { DB::commit(); }

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error. ' . $e;
            DB::rollBack();
        }

        return $this->jsonReturnResponse($success, $msg, $data);
    }

    // [c]
    public function register_associate(Request $request) {

        $success = false;
        $msg = '';
        $data = [];

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'file' => 'required',
        ]);

        if($validator->fails()) {
            $msg = "[title, file], 지정된 데이터를 모두 입력하세요.";
            return $this->jsonReturnResponse($success, $msg, $data);
        }

        try {

            DB::beginTransaction();

            $admin_id = Auth::user()->id;

            if(Associate::create($request->all(), $admin_id)) {
                DB::commit();
            }

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error. ';
            DB::rollBack();
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // 사용하지 않습니다.
    public function register_introduce(Request $request) {

        $success = false;
        $msg = '';
        $data = [];

        $validator = Validator::make($request->all(), [
            'pc_id',
            'mb_id',
            'pc_file' => 'required',
            'mb_file' => 'required',
        ]);

        if($validator->fails()) {
            $msg = "지정된 데이터를 모두 입력하세요.";
            return $this->jsonReturnResponse($success, $msg, $data);
        }

        try {

            DB::beginTransaction();

            $admin_id = Auth::user()->id;

            if(Associate::create($request->all(), $admin_id)) {
                DB::commit();
            }

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error. ' .$e;
            DB::rollBack();
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // [c]
    public function register_guide(Request $request) {

        $success = false;
        $msg = '';
        $data = [];

        $validator = Validator::make($request->all(), [
            'top' => 'required',
            'title' => 'required',
            'content' => 'required',
        ]);

        if($validator->fails()) {
            $msg = "[top, title, content],지정된 데이터를 모두 입력하세요.";
            return $this->jsonReturnResponse($success, $msg, $data);
        }

        try {

            DB::beginTransaction();

            $admin_id = Auth::user()->id;

            $guideId = null;

            if($guideId=Guide::create($request->all(), $admin_id)) {
                BoardFiles::uploadFiles($request->all(), $guideId, $admin_id, 0);
                DB::commit();
            }

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error. ';
            DB::rollBack();
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // [c]
    public function register_notice(Request $request) {

        $success = false;
        $msg = '';
        $data = [];

        $validator = Validator::make($request->all(), [
            'top' => 'required',
            'title' => 'required',
            'content' => 'required',
        ]);

        if($validator->fails()) {
            $msg = "[top, title, content],지정된 데이터를 모두 입력하세요.";
            return $this->jsonReturnResponse($success, $msg, $data);
        }

        try {

            DB::beginTransaction();

            $admin_id = Auth::user()->id;

            $noticeId = null;

            if($noticeId=Notice::create($request->all(), $admin_id)) {
                BoardFiles::uploadFiles($request->all(), $noticeId, $admin_id, 1);
                DB::commit();
            }

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error. ' . $e;
            DB::rollBack();
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // [c]
    public function register_faq(Request $request) {

        $success = false;
        $msg = '';
        $data = [];

        $validator = Validator::make($request->all(), [
            'category_id' => 'required',
            'title' => 'required',
            'content' => 'required',
        ]);

        if($validator->fails()) {
            $msg = "[category_id, title, content],지정된 데이터를 모두 입력하세요.";
            return $this->jsonReturnResponse($success, $msg, $data);
        }

        try {

            DB::beginTransaction();

            if(Faq::create($request->all())) {
                DB::commit();
            }

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error. Register Faq';
            DB::rollBack();
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // [c]
    public function register_contest(Request $request) {

        $success = false;
        $msg = '';
        $data = [];

        $validator = Validator::make($request->all(), [
            'status'                => 'required', // 공모상태
            'title'                 => 'required',
            'feature'               => 'required',
            'description'           => 'required',
            'content'               => 'required',
            'cost'                  => 'required|',
            'post_code'             => 'required',
            'address'               => 'required',
            'address_detail'        => 'required',
            'real_stocked_at'       => 'required',
            'opened_at'             => 'required',
            'closed_at'             => 'required',
            'can_buy_stock' => 'required',
            'expect_return_amount' => 'required',
            'closed_dividend_at'    => 'required',
            'info_title'            => 'required',
            'info_row'              => 'required',
            'main_image_file'       => 'required',
        ]);

        if($validator->fails()) {
            $msg = "[status, title, feature, description, content, post_code, address, address_detail, real_stocked_at, opened_at, closed_at, can_buy_stock, expect_return_amount, closed_dividend_at, info_title,info_row, main_image_file], 지정된 데이터를 모두 입력하세요.";
            return $this->jsonReturnResponse($success, $msg, $data);
        }

        try {

            DB::beginTransaction();

            $admin_id = Auth::user()->id;

            $contestId = null;

            if($contestId=Contest::create($request->all(), $admin_id)) {
                ContestInfo::create($request->all(), $admin_id, $contestId);
                ContestFiles::uploadFiles($request->all(), $admin_id, $contestId);
                DB::commit();
            }

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error. Register Contest ' . $e;
            DB::rollBack();
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // [c]
    public function register_dividend(Request $request) {

        $success = false;
        $msg = '';
        $data = [];

        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if($validator->fails()) {
            $msg = "[id],지정된 데이터를 모두 입력하세요.";
            return $this->jsonReturnResponse($success, $msg, $data);
        }

        try {

            DB::beginTransaction();

            // contest_id
            $contest_id = $request->input('id');

            // 전체 배당금액
            $amount = $request->input('amount');

            $admin_id = Auth::user()->id;

            $option = Contest::admin_get_contest_option($contest_id);

            UserDividend::admin_register_deposit($contest_id, $amount, $option, $admin_id);
            DB::commit();
            $success = true;

        } catch (\Exception $e) {
            $msg = '배당지급에 실패하였습니다. 배당지급 기록이 취소되었습니다.' . $e;
            DB::rollBack();
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // [c] 배당관리 : 투자자 추가 step3 관리자 투자 진행
    public function register_dividend_user(Request $request) {

        // 배당 상세 투자자 추가 저장 처리
        $success = false;
        $msg = '';
        $data = [];

        $validator = Validator::make($request->all(), [
            'cid'       => 'required',
            'uid'       => 'required',
            'type'      => 'required',
            'amount'    => 'required',
            'token'     => 'required',
        ]);

        if($validator->fails()) {
            $msg = "[cid, uid, type, amount, token],지정된 데이터를 모두 입력하세요.";
            return $this->jsonReturnResponse($success, $msg, $data);
        }

        try {

            $contest_id = $request->input('cid');
            $user_id    = $request->input('uid');
            $estate_id  = $request->input('eid');
            $type       = $request->input('type');
            $amount     = $request->input('amount');
            $token      = $request->input('token');

            $admin_id = Auth::user()->id;

            DB::beginTransaction();

            // 투자 시점 해당 공모전의 토큰 가격
            $tokenAmount = (int) \App\Models\Contest::getContestTokenAmount($contest_id)->token_amount;

            // 투자 시점 유저가 투자한 토큰 개수
            $user_buy_token_count = (int)$token;

            // 투자 시점 유저가 투자한 금액
            $user_buy_amount = (int)$amount;

            $data['1'] = $tokenAmount;
            $data['2'] = $user_buy_token_count;
            $data['3'] = $user_buy_amount;

            // 투자 시점 기준, 사용자로부터 전달 받은 데이터가 유효한지 체크한다. [ 전체 투자 금액 / 공모전 토큰 개당 가격 == 전체 투자 금액 비례 토큰 개수 ]
            if($user_buy_amount / $tokenAmount == $user_buy_token_count) {

                // 1. 투자 시점 기준, 공모전 투자 진행 현황 대비 사용자가 투자한 금액으로 인해 초과되는지 체크한다.
                $checkContestMaxStockAmountOver = \App\Models\Contest::stockOverCheck($contest_id, $user_buy_token_count, $user_buy_amount);

                if(!$checkContestMaxStockAmountOver) {
                    $msg = '비정상적인 투자 진행이 감지되었습니다. [1]';
                    return response()->json([ 'success' => $success, 'msg' => $msg, 'data' => $data ]);
                }

                // 2. 투자 시점 및 투자 방법 기준 : 예치금 사용 시, 보유하고 있는 예치금 내에서 투자 진행 가능한지 체크한다.
                $user_assets_over_check = false;

                if($type == 0
                    && \App\Models\UserAssets::stockOverCheck($user_id, $user_buy_amount))
                {
                    $user_assets_over_check = true;
                }

                // 3. 투자 시점 및 투자 방법 기준 : 전세 자금 사용 시, 해당 전세 자금 내에서 투자 진행 가능한지 체크한다.
                if($type == 1
                    && \App\Models\UserEstate::stockOverCheck($user_id, $user_buy_amount, $estate_id))
                {
                    $user_assets_over_check = true;
                }

                if(!$user_assets_over_check) {
                    $msg = '심각. 비정상적인 투자 진행이 감지되었습니다. [Over]';
                    return response()->json([ 'success' => $success, 'msg' => $msg, 'data' => $data ]);
                }

                // 투자가 정상적으로 진행 완료될 경우 모든 데이터를 저장한다.
                if(\App\Models\UserStock::stocked($user_id, $tokenAmount, $request->all(), $admin_id))
                {
                    // 투자 완료 이후, 회원 자산 정보 업데이트 및 공모전 현재 투자 금액 업데이트
                    \App\Models\UserAssets::minusStockAssests($user_id, $type, $user_buy_amount, $admin_id);
                    \App\Models\Contest::userStockedUpdateContestCost($contest_id, $user_buy_amount);

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

        return $this->jsonReturnResponse($success, $msg, $data);
    }

    // [c]
    public function register_estate_notice(Request $request) {

        $success = false;
        $msg = '';
        $data = [];

        $validator = Validator::make($request->all(), [
            'eid' => 'required',
            'type' => 'required',
            'title' => 'required',
            'content' => 'required',
        ]);

        if($validator->fails()) {
            $msg = "[cid, type, title, content],지정된 데이터를 모두 입력하세요.";
            return $this->jsonReturnResponse($success, $msg, $data);
        }

        try {

            DB::beginTransaction();

            $admin_id = Auth::user()->id;
            $document_id = null;

            if ($document_id=UserEstateAdminNotice::admin_register_content($request->all(), $admin_id)) {
                if($request->has('files')) {
                    // contest_id, files, type, admin_id
                    UserEstateFiles::admin_upload_files($document_id, $request->file('files'), 2);
                }
                DB::commit();
                $success = true;
            }


        } catch (\Exception $e) {
            $msg = 'Error. '.$e;
            DB::rollBack();
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // [c]
    public function register_estate_contractor(Request $request) {

        $success = false;
        $msg = '';
        $data = [];

        $validator = Validator::make($request->all(), [
            'eid' => 'required',
            'title' => 'required',
            'content' => 'required',
        ]);

        if($validator->fails()) {
            $msg = "[cid, type, title, content],지정된 데이터를 모두 입력하세요.";
            return $this->jsonReturnResponse($success, $msg, $data);
        }

        try {

            DB::beginTransaction();

            $admin_id = Auth::user()->id;
            $document_id = null;

            if ($document_id=UserEstateContractor::admin_register_content($request->all(), $admin_id)) {
                if($request->has('files')) {
                    // contest_id, files, type, admin_id
                    UserEstateFiles::admin_upload_files($document_id, $request->file('files'), 0);
                }
                DB::commit();
                $success = true;
            }


        } catch (\Exception $e) {
            $msg = 'Error. '.$e;
            DB::rollBack();
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }


    /* ---------------------------------------------------------------------------------- */
    // [c]
    public function detail_popup(Request $request) {

        $success = false;
        $msg = '';
        $data = [];

        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if($validator->fails()) {
            $msg = "[id],지정된 데이터를 모두 입력하세요.";
            return $this->jsonReturnResponse($success, $msg, $data);
        }

        try {

            $data['detail'] = Popup::detail($request->input('id'));

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error. ';
        }

        return $this->jsonReturnResponse($success, $msg, $data);
    }

    // [c]
    public function detail_banner(Request $request) {
        $success = false;
        $msg = '';
        $data = [];

        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if($validator->fails()) {
            $msg = "[id], 지정된 데이터를 모두 입력하세요.";
            return $this->jsonReturnResponse($success, $msg, $data);
        }

        try {

            $data['detail'] = Banner::detail($request->input('id'));

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error. ' . $e;
        }

        return $this->jsonReturnResponse($success, $msg, $data);
    }

    // [c]
    public function detail_associate(Request $request) {

        $success = false;
        $msg = '';
        $data = [];

        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if($validator->fails()) {
            $msg = "[id],지정된 데이터를 모두 입력하세요.";
            return $this->jsonReturnResponse($success, $msg, $data);
        }

        try {

            $data['detail'] = Associate::detail($request->input('id'));
            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error. ';
            DB::rollBack();
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // 사용하지 않습니다.
    public function detail_introduce(Request $request) {

        $success = false;
        $msg = '';
        $data = [];

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'url' => 'required',
            'file' => 'required',
        ]);

        if($validator->fails()) {
            $msg = "지정된 데이터를 모두 입력하세요.";
            return $this->jsonReturnResponse($success, $msg, $data);
        }

        try {

            DB::beginTransaction();


            DB::commit();

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error. ';
            DB::rollBack();
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // [c]
    public function detail_guide(Request $request) {

        $success = false;
        $msg = '';
        $data = [];

        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if($validator->fails()) {
            $msg = "[id], 지정된 데이터를 모두 입력하세요.";
            return $this->jsonReturnResponse($success, $msg, $data);
        }

        try {
            $id = $request->input('id');
            $data['detail'] = Guide::admin_detail($id);

            // getBoardFiles( , 0) -> 0 : guide, 1: notice
            $data['files'] = BoardFiles::getBoardFiles($id, 0);

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error. ';
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // [c]
    public function detail_notice(Request $request) {

        $success = false;
        $msg = '';
        $data = [];

        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if($validator->fails()) {
            $msg = "[id], 지정된 데이터를 모두 입력하세요.";
            return $this->jsonReturnResponse($success, $msg, $data);
        }

        try {
            $id = $request->input('id');
            $data['detail'] = Notice::admin_detail($id);

            // getBoardFiles( , 0) -> 0 : guide, 1: notice
            $data['files'] = BoardFiles::getBoardFiles($id, 1);

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error. ';
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // [c]
    public function detail_faq(Request $request) {

        $success = false;
        $msg = '';
        $data = [];

        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if($validator->fails()) {
            $msg = "[id],지정된 데이터를 모두 입력하세요.";
            return $this->jsonReturnResponse($success, $msg, $data);
        }

        try {

            $data['detail'] = Faq::admin_detail($request->input('id'));
            $data['category'] = CategoryBoard::list();
            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error. FAQ';
            DB::rollBack();
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // [c] 배당 관리 상세
    public function detail_dividend(Request $request) {

        $success = false;
        $msg = '';
        $data = [];

        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if($validator->fails()) {
            $msg = "[id],지정된 데이터를 모두 입력하세요.";
            return $this->jsonReturnResponse($success, $msg, $data);
        }

        try {

            // this id is contest id
            $id = $request->input('id');

            $data['detail'] = Contest::admin_detail_dividend($id);
            $data['dividend_count'] = UserDividend::admin_get_dividend_contest_count($id);
            $data['stock_list'] = UserStock::admin_contest_stock_people_list($id);

            $success = true;


        } catch (\Exception $e) {
            $msg = 'Error. '.$e;
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // [c]
    public function detail_contest(Request $request) {

        $success = false;
        $msg = '';
        $data = [];

        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if($validator->fails()) {
            $msg = "[id],지정된 데이터를 모두 입력하세요.";
            return $this->jsonReturnResponse($success, $msg, $data);
        }

        try {
            $id = $request->input('id');

            $data['detail']         = Contest::admin_detail($id);
            $data['info']           = ContestInfo::admin_detail_info($id);
            $data['main_images']    = ContestFiles::admin_detail_get_main_image_files($id);
            $data['slider_images']  = ContestFiles::admin_detail_get_files($id, 1);
            $data['files']          = ContestFiles::admin_detail_get_files($id, 2);
            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error. ' . $e;
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // [c] 배당상세 - 투자자 추가 : 유저 상세 정보
    public function detail_user(Request $request) {

        $success = false;
        $msg = '';
        $data = [];

        $validator = Validator::make($request->all(), [
            'cid' => 'required',
            'uid' => 'required',
        ]);

        if($validator->fails()) {
            $msg = "[cid, uid],지정된 데이터를 모두 입력하세요.";
            return $this->jsonReturnResponse($success, $msg, $data);
        }

        try {

            $cid = $request->input('cid');
            $uid = $request->input('uid');

            $data['stockedByToken'] = (int)\App\Models\UserStock::getStockedBuyTokenCount($uid, $cid);
            $data['can_use_amount'] = (int)\App\Models\UserAssets::getCanUseMyAmount($uid);
            $data['can_stock_estate_my_list'] = \App\Models\UserEstate::getMyEstateList($uid, $cid);
            $data['stocked_estate_history'] = \App\Models\UserStock::getEstateStockedHistory($uid, $cid);
            $data['totalSumEstateAmount'] = (int)\App\Models\UserEstate::jeonse_total_assets($uid) - (int)\App\Models\UserStock::getStockedEstateAmount($uid) ;

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error. ';
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // [c]
    public function detail_estate(Request $request) {

        $success = false;
        $msg = '';
        $data = [];

        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if($validator->fails()) {
            $msg = "[id],지정된 데이터를 모두 입력하세요.";
            return $this->jsonReturnResponse($success, $msg, $data);
        }

        try {

            $estate_id = $request->input('id');

            $data['detail']     = UserEstate::admin_detail_contest($estate_id);
            $data['notice']     = UserEstateAdminNotice::admin_detail_contest($estate_id);
            $data['contractor'] = UserEstateContractor::admin_detail_contest($estate_id);
            $data['qna']        = UserEstateQuestion::admin_detail_contest($estate_id);

            $data['code'] = [
                'detail' => [
                    "approve" => '0: 비승인, 1: 승인',
                ],
                'notice' => "0: 임대인, 1:임차인, 2:모두",
                'QNA' => "0:처리요청, 1:처리보류, 2:승인완료, 3:관리자"
            ];

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error. '.$e;
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // [c]
    public function detail_estate_notice(Request $request) {

        $success = false;
        $msg = '';
        $data = [];

        $validator = Validator::make($request->all(), [
            'eid' => 'required',
            'did' => 'required',
        ]);

        if($validator->fails()) {
            $msg = "[id],지정된 데이터를 모두 입력하세요.";
            return $this->jsonReturnResponse($success, $msg, $data);
        }

        try {

            $eid = $request->input('eid');
            $did = $request->input('did');

            $data['detail'] = UserEstateAdminNotice::admin_detail_content($eid, $did);
            $data['files'] = UserEstateFiles::admin_get_files($did, 2);

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error. ';
            DB::rollBack();
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // [c]
    public function detail_estate_contractor(Request $request) {

        $success = false;
        $msg = '';
        $data = [];

        $validator = Validator::make($request->all(), [
            'eid' => 'required',
            'did' => 'required',
        ]);

        if($validator->fails()) {
            $msg = "[id],지정된 데이터를 모두 입력하세요.";
            return $this->jsonReturnResponse($success, $msg, $data);
        }

        try {

            $eid = $request->input('eid');
            $did = $request->input('did');

            $data['detail'] = UserEstateContractor::admin_detail_content($eid, $did);
            $data['files'] = UserEstateFiles::admin_get_files($did, 0);

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error. ';
            DB::rollBack();
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // [c]
    protected function detail_estate_qna(Request $request) {

        $success = false;
        $msg = '';
        $data = [];

        $validator = Validator::make($request->all(), [
            'eid' => 'required',
            'did' => 'required',
        ]);

        if($validator->fails()) {
            $msg = "[eid, did],지정된 데이터를 모두 입력하세요.";
            return $this->jsonReturnResponse($success, $msg, $data);
        }

        try {

            DB::beginTransaction();
            $eid = $request->input('eid');
            $did = $request->input('did');

            $data['detail'] = UserEstateQuestion::admin_detail_content($eid, $did);
            $data['category'] = UserEstateQuestionCategory::admin_get_detail_category();
            $data['files'] = UserEstateFiles::admin_get_files($did, 1);

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error. ';
            DB::rollBack();
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    /* ---------------------------------------------------------------------------------- */
    // [c]
    public function update_popup(Request $request) {

        $success = false;
        $msg = '';
        $data = [];

        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if($validator->fails()) {
            $msg = "[id], 지정된 데이터를 모두 입력하세요.";
            return $this->jsonReturnResponse($success, $msg, $data);
        }

        try {

            DB::beginTransaction();

            if(Popup::updateContent($request->all())) { DB::commit(); }

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error. ';
            DB::rollBack();
        }

        return $this->jsonReturnResponse($success, $msg, $data);
    }

    // [c]
    public function update_banner(Request $request) {

        $success = false;
        $msg = '';
        $data = [];

        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if($validator->fails()) {
            $msg = "[id], 지정된 데이터를 모두 입력하세요.";
            return $this->jsonReturnResponse($success, $msg, $data);
        }

        try {

            DB::beginTransaction();

            $admin_id = Auth::user()->id;

            if(Banner::updateContent($request->all(), $admin_id)) { DB::commit(); }

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error. ';
            DB::rollBack();
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // [c]
    public function update_associate(Request $request) {

        $success = false;
        $msg = '';
        $data = [];

        $validator = Validator::make($request->all(), [
            'pc_id' => 'required',
            'mobile_id' => 'required',

        ]);

        if($validator->fails()) {
            $msg = "[id],지정된 데이터를 모두 입력하세요.";
            return $this->jsonReturnResponse($success, $msg, $data);
        }

        try {

            DB::beginTransaction();

            $admin_id = Auth::user()->id;

            if(Associate::updateContent($request->all(), $admin_id)) {
                DB::commit();
            }

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error. ';
            DB::rollBack();
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // [c]
    public function update_introduce(Request $request) {

        $success = false;
        $msg = '';
        $data = [];

        try {

            DB::beginTransaction();

            $admin_id = Auth::user()->id;

            if(About::updateContent($request->all(), $admin_id)) {
                DB::commit();
            }

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error. ' . $e;
            DB::rollBack();
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // [c]
    public function update_guide(Request $request) {

        $success = false;
        $msg = '';
        $data = [];

        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'top' => 'required',
        ]);

        if($validator->fails()) {
            $msg = "[id, top],지정된 데이터를 모두 입력하세요.";
            return $this->jsonReturnResponse($success, $msg, $data);
        }

        try {

            DB::beginTransaction();

            $admin_id = Auth::user()->id;

            if (Guide::updateContent($request->all(), $admin_id)) {

                BoardFiles::updateFiles($request->all(), 0, $admin_id);
                DB::commit();
            }

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error. ' . $e;
            DB::rollBack();
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // [c]
    public function update_notice(Request $request) {

        $success = false;
        $msg = '';
        $data = [];

        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'top' => 'required',
        ]);

        if($validator->fails()) {
            $msg = "[id, top],지정된 데이터를 모두 입력하세요.";
            return $this->jsonReturnResponse($success, $msg, $data);
        }

        try {

            DB::beginTransaction();

            $admin_id = Auth::user()->id;

            if (Notice::updateContent($request->all(), $admin_id)) {

                BoardFiles::updateFiles($request->all(), 1, $admin_id);
                DB::commit();
            }

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error. ' . $e;
            DB::rollBack();
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // [c]
    public function update_faq(Request $request) {

        $success = false;
        $msg = '';
        $data = [];

        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if($validator->fails()) {
            $msg = "[id],지정된 데이터를 모두 입력하세요.";
            return $this->jsonReturnResponse($success, $msg, $data);
        }

        try {

            DB::beginTransaction();

            $admin_id = Auth::user()->id;

            if(Faq::updateContent($request->input(), $admin_id)) {
                DB::commit();
            }

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error. Update Faq';
            DB::rollBack();
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // [c]
    public function update_user_leaved(Request $request) {

        $success = false;
        $msg = '';
        $data = [];

        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if($validator->fails()) {
            $msg = "[id],지정된 데이터를 모두 입력하세요.";
            return $this->jsonReturnResponse($success, $msg, $data);
        }

        try {

            DB::beginTransaction();

            if(User::admin_user_leaved($request->input('id'))) {
                LeavedUser::admin_update_user_leaved($request->input('id'));
                DB::commit();
            }


            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error. ';
            DB::rollBack();
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // [c]
    public function update_contest(Request $request) {

        $success = false;
        $msg = '';
        $data = [];

        $validator = Validator::make($request->all(), [
            'status'                => 'required', // 공모상태
            'title'                 => 'required',
            'feature'               => 'required',
            'description'           => 'required',
            'content'               => 'required',
            'cost'                  => 'required|',
            'address'               => 'required',
            'real_stocked_at'       => 'required',
            'opened_at'             => 'required',
            'closed_at'             => 'required',
            'can_buy_stock'         => 'required',
            'expect_return_amount'  => 'required',
            'closed_dividend_at'    => 'required',
            'info_title'            => 'required',
        ]);

        if($validator->fails()) {
            $msg = "[status, title, feature, description, content, address, real_stocked_at, opened_at, closed_at, can_buy_stock, expect_return_amount, closed_dividend_at, info_title], 지정된 데이터를 모두 입력하세요.";
            return $this->jsonReturnResponse($success, $msg, $data);
        }

        try {

            DB::beginTransaction();

            $admin_id = Auth::user()->id;

            if(Contest::updateContent($request->all(), $admin_id)) {
                ContestInfo::updateInfo($request->all(), $admin_id);
                ContestFiles::updateFiles($request->all(), $admin_id);
                DB::commit();
            }

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error. Register Contest ' . $e;
            DB::rollBack();
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // [c]
    protected function update_estate(Request $request) {

        $success = false;
        $msg = '';
        $data = [];

        $validator = Validator::make($request->all(), [
            'eid'                       => 'required',
            'title'                     => 'required',
            'contractor_total_amount'   => 'required',
            'contractor_open_at'        => 'required',
            'contractor_closed_at'      => 'required',
            'approve'                   => 'required',
            'post_code'                 => 'required',
            'address'                   => 'required',
            'lessor_name'               => 'required',
            'lessor_phone'              => 'required',
            'password'                  => 'required',
        ]);

        if($validator->fails()) {
            $msg = "[
                eid,
                title,
                contractor_total_amount,
                contractor_open_at,
                contractor_closed_at,
                approve,
                post_code,
                address,
                lessor_name,
                lessor_phone,
                password
            ], 지정된 데이터를 모두 입력하세요.";
            return $this->jsonReturnResponse($success, $msg, $data);
        }

        try {

            DB::beginTransaction();

            $admin_id = Auth::user()->id;

            if (UserEstate::admin_update_content($request->all(), $admin_id)) {
                DB::commit();
                $success = true;
            }

        } catch (\Exception $e) {
            $msg = 'Error. '.$e;
            DB::rollBack();
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // [c]
    protected function update_estate_notice(Request $request) {

        $success = false;
        $msg = '';
        $data = [];

        $validator = Validator::make($request->all(), [
            'eid' => 'required',
            'did' => 'required',
            'type' => 'required',
            'title' => 'required',
            'content' => 'required',
        ]);

        if($validator->fails()) {
            $msg = "[eid, did, type, title, content],지정된 데이터를 모두 입력하세요.";
            return $this->jsonReturnResponse($success, $msg, $data);
        }

        try {

            DB::beginTransaction();
            $admin_id = Auth::user()->id;
            $req = $request->all();

            if (UserEstateAdminNotice::admin_update_content($req, $admin_id)) {

                UserEstateFiles::admin_update_files($req, 2);

                DB::commit();

                $success = true;
            }


        } catch (\Exception $e) {
            $msg = 'Error. ';
            DB::rollBack();
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // [c]
    protected function update_estate_contractor(Request $request) {

        $success = false;
        $msg = '';
        $data = [];

        $validator = Validator::make($request->all(), [
            'eid' => 'required',
            'did' => 'required',
            'title' => 'required',
            'content' => 'required',
        ]);

        if($validator->fails()) {
            $msg = "[eid, did, type, title, content],지정된 데이터를 모두 입력하세요.";
            return $this->jsonReturnResponse($success, $msg, $data);
        }

        try {

            DB::beginTransaction();

            $admin_id = Auth::user()->id;
            $req = $request->all();

            if (UserEstateContractor::admin_update_content($req, $admin_id)) {

                UserEstateFiles::admin_update_files($req, 0);

                DB::commit();

                $success = true;
            }


        } catch (\Exception $e) {
            $msg = 'Error. ';
            DB::rollBack();
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // [c]
    protected function update_estate_qna(Request $request) {

        $success = false;
        $msg = '';
        $data = [];

        $validator = Validator::make($request->all(), [
            'eid' => 'required',
            'ref_admin_approve' => 'required',
            'did' => 'required',
            'title' => 'required',
            'content' => 'required',
            'category' => 'required',
            'type' => 'required',
        ]);

        if($validator->fails()) {
            $msg = "[eid, did, ref_admin_approve, type, title, content],지정된 데이터를 모두 입력하세요.";
            return $this->jsonReturnResponse($success, $msg, $data);
        }

        try {

            DB::beginTransaction();

            $admin_id = Auth::user()->id;
            $req = $request->all();

            if (UserEstateQuestion::admin_update_content($req, $admin_id)) {

                UserEstateFiles::admin_update_files($req, 1);

                DB::commit();

                $success = true;
            }


        } catch (\Exception $e) {
            $msg = 'Error. ';
            DB::rollBack();
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // [ ... ing ] 추가 의견 검토 필요
    protected function update_user_assets(Request $request) {

        $success = false;
        $msg = '';
        $data = [];

        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if($validator->fails()) {
            $msg = "[id, type, amount],지정된 데이터를 모두 입력하세요.";
            return $this->jsonReturnResponse($success, $msg, $data);
        }

        try {

            DB::beginTransaction();

            $admin_id = Auth::user()->id;

            $user_id        = $request->input('id');
            $type           = $request->input('type'); // 0:예치금, 1:전세금
            $amount         = $request->input('amount'); // 입금 금액
            $estate_amount  = $request->input('estate_amount'); // 입금 금액

            UserAssets::admin_update_user_assets($user_id, $type, $amount, $estate_amount, $admin_id);

            DB::commit();
            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error. Update Admin User Assets' . $e;
            DB::rollBack();
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // [c] 출금 관리 승인 처리
    protected function update_withdraw(Request $request) {
        $success = false;
        $msg = '';
        $data = [];

        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if($validator->fails()) {
            $msg = "[id],지정된 데이터를 모두 입력하세요.";
            return $this->jsonReturnResponse($success, $msg, $data);
        }

        try {

            DB::beginTransaction();
            $id = $request->input('id');

            if (UserRequestWithDrawal::admin_update_content($id)) {
                DB::commit();
                $success = true;
            }


        } catch (\Exception $e) {
            $msg = 'Error. ';
            DB::rollBack();
        }

        return $this->jsonReturnResponse($success, $msg, $data);
    }

    // [c] 입금 처리 취소 : 재확인 필요
    protected function update_deposit(Request $request) {

        $success = false;
        $msg = '';
        $data = [];

        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if($validator->fails()) {
            $msg = "[id],지정된 데이터를 모두 입력하세요.";
            return $this->jsonReturnResponse($success, $msg, $data);
        }

        try {

            DB::beginTransaction();
            $id = $request->input('id');

            $admin_id = Auth::user()->id;

            $code = [];

            if ($code=UserAssets::admin_update_user_cancle_assets($id, $admin_id)) {
                if($code['key']==1) {
                    DB::commit();
                    $success = true;
                } else {
                    $msg = '현재 회원 자산에서 ' . $code['amount'] . ' 금액을 취소할 경우 (-) 처리가 됨으로 실행을 완료할 수 없습니다.';
                }
            }


        } catch (\Exception $e) {
            $msg = 'Error. ';
            DB::rollBack();
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    /* ---------------------------------------------------------------------------------- */
    // [c]
    public function delete_popup(Request $request) {

        $success = false;
        $msg = '';
        $data = [];

        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if($validator->fails()) {
            $msg = "[id],지정된 데이터를 모두 입력하세요.";
            return $this->jsonReturnResponse($success, $msg, $data);
        }

        try {

            DB::beginTransaction();

            if(Popup::deleteContent($request->input('id'))) { DB::commit(); }

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error. Delete Popup';
            DB::rollBack();
        }

        return $this->jsonReturnResponse($success, $msg, $data);
    }

    // [c]
    public function delete_banner(Request $request) {
        $success = false;
        $msg = '';
        $data = [];

        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if($validator->fails()) {
            $msg = "[id], 지정된 데이터를 모두 입력하세요.";
            return $this->jsonReturnResponse($success, $msg, $data);
        }

        try {

            DB::beginTransaction();

            if(Banner::deleteContent($request->input('id'))) { DB::commit(); }

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error. Delete Banner';
            DB::rollBack();
        }

        return $this->jsonReturnResponse($success, $msg, $data);
    }

    // [c]
    public function delete_associate(Request $request) {

        $success = false;
        $msg = '';
        $data = [];

        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if($validator->fails()) {
            $msg = "[id],지정된 데이터를 모두 입력하세요.";
            return $this->jsonReturnResponse($success, $msg, $data);
        }

        try {

            DB::beginTransaction();

            if(Associate::deleteContent($request->input('id'))) {
                DB::commit();
            }

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error. Admin delete associate';
            DB::rollBack();
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // 사용하지 않습니다.
    public function delete_introduce(Request $request) {

        $success = false;
        $msg = '';
        $data = [];

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'url' => 'required',
            'file' => 'required',
        ]);

        if($validator->fails()) {
            $msg = "지정된 데이터를 모두 입력하세요.";
            return $this->jsonReturnResponse($success, $msg, $data);
        }

        try {

            DB::beginTransaction();


            DB::commit();

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error. ';
            DB::rollBack();
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // [c]
    public function delete_guide(Request $request) {

        $success = false;
        $msg = '';
        $data = [];

        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if($validator->fails()) {
            $msg = "[id],지정된 데이터를 모두 입력하세요.";
            return $this->jsonReturnResponse($success, $msg, $data);
        }

        try {

            DB::beginTransaction();
            $id = $request->input('id');
            $admin_id = Auth::user()->id;

            if (Guide::deleteContent($id, $admin_id)) {
                DB::commit();
                $success = true;
            }


        } catch (\Exception $e) {
            $msg = 'Error. ';
            DB::rollBack();
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // [c]
    public function delete_notice(Request $request) {

        $success = false;
        $msg = '';
        $data = [];

        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if($validator->fails()) {
            $msg = "[id],지정된 데이터를 모두 입력하세요.";
            return $this->jsonReturnResponse($success, $msg, $data);
        }

        try {

            DB::beginTransaction();

            $admin_id = Auth::user()->id;
            $id = $request->input('id');

            if(Notice::deleteContent($id, $admin_id)) {
                // deleteFiles( , 0) 0:guide, 1:notice
                BoardFiles::deleteFiles($id, 1);
                DB::commit();
            }

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error. ';
            DB::rollBack();
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // [c]
    public function delete_faq(Request $request) {

        $success = false;
        $msg = '';
        $data = [];

        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if($validator->fails()) {
            $msg = "[id],지정된 데이터를 모두 입력하세요.";
            return $this->jsonReturnResponse($success, $msg, $data);
        }

        try {

            DB::beginTransaction();

            if(Faq::deleteContent($request->input('id'))) {
                DB::commit();
            }

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error. ';
            DB::rollBack();
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // [c]
    public function delete_leaved_user(Request $request) {

        $success = false;
        $msg = '';
        $data = [];

        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if($validator->fails()) {
            $msg = "[id],지정된 데이터를 모두 입력하세요.";
            return $this->jsonReturnResponse($success, $msg, $data);
        }

        try {

            DB::beginTransaction();

            if (User::admin_user_leaved($request->input('id'))) {
                LeavedUser::admin_delete_leaved_list($request->input('id'));
                DB::commit();
                $success = true;
            }


        } catch (\Exception $e) {
            $msg = 'Error. ';
            DB::rollBack();
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // [c]
    public function delete_contest(Request $request) {

        $success = false;
        $msg = '';
        $data = [];

        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if($validator->fails()) {
            $msg = "[id],지정된 데이터를 모두 입력하세요.";
            return $this->jsonReturnResponse($success, $msg, $data);
        }

        try {

            DB::beginTransaction();
            $id = $request->input('id');

            if (Contest::admin_delete_contest($id)) {
                DB::commit();
                $success = true;
            }


        } catch (\Exception $e) {
            $msg = 'Error. ';
            DB::rollBack();
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // [c]
    public function delete_dividend(Request $request) {

        $success = false;
        $msg = '';
        $data = [];

        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if($validator->fails()) {
            $msg = "[id],지정된 데이터를 모두 입력하세요.";
            return $this->jsonReturnResponse($success, $msg, $data);
        }

        try {

            DB::beginTransaction();
            $id = $request->input('id');

            if(Contest::admin_delete_dividend_list($id)) {
                DB::commit();
                $success = true;
            }

        } catch (\Exception $e) {
            $msg = 'Error. ';
            DB::rollBack();
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // [c]
    public function delete_dividend_user(Request $request) {

        $success = false;
        $msg = '';
        $data = [];

        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'contest_id' => 'required',
        ]);

        if($validator->fails()) {
            $msg = "[id],지정된 데이터를 모두 입력하세요.";
            return $this->jsonReturnResponse($success, $msg, $data);
        }

        try {

            DB::beginTransaction();
            $user_id = $request->input('user_id');
            $contest_id = $request->input('contest_id');

            if (UserStock::admin_delete_stock_user($user_id, $contest_id)) {
                DB::commit();
                $success = true;
            }


        } catch (\Exception $e) {
            $msg = 'Error. ';
            DB::rollBack();
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // [...ing] : 고객사 측과 협의 후 상세 정책 및 방안 정의가 필요하다. 23-01-09 min
    public function delete_estate(Request $request) {

        $success = false;
        $msg = '';
        $data = [];

        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if($validator->fails()) {
            $msg = "[id],지정된 데이터를 모두 입력하세요.";
            return $this->jsonReturnResponse($success, $msg, $data);
        }

        try {

            DB::beginTransaction();
            $id = $request->input('id');

            if (UserEstate::admin_delete_content($id)) {
                DB::commit();
                $success = true;
            }

        } catch (\Exception $e) {
            $msg = 'Error. ';
            DB::rollBack();
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // [c]
    public function delete_estate_notice(Request $request) {

        $success = false;
        $msg = '';
        $data = [];

        $validator = Validator::make($request->all(), [
            'eid' => 'required',
            'did' => 'required',
        ]);

        if($validator->fails()) {
            $msg = "[eid, did],지정된 데이터를 모두 입력하세요.";
            return $this->jsonReturnResponse($success, $msg, $data);
        }

        try {

            DB::beginTransaction();
            $eid = $request->input('eid');
            $did = $request->input('did');

            if (UserEstateAdminNotice::admin_delete_content($eid, $did)) {
                DB::commit();
                $success = true;
            }

        } catch (\Exception $e) {
            $msg = 'Error. ' . $e;
            DB::rollBack();
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // [c]
    public function delete_estate_contractor(Request $request) {

        $success = false;
        $msg = '';
        $data = [];

        $validator = Validator::make($request->all(), [
            'eid' => 'required',
            'did' => 'required',
        ]);

        if($validator->fails()) {
            $msg = "[eid, did],지정된 데이터를 모두 입력하세요.";
            return $this->jsonReturnResponse($success, $msg, $data);
        }

        try {

            DB::beginTransaction();
            $eid = $request->input('eid');
            $did = $request->input('did');

            if (UserEstateContractor::admin_delete_content($eid, $did)) {
                DB::commit();
                $success = true;
            }


        } catch (\Exception $e) {
            $msg = 'Error. ';
            DB::rollBack();
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // [c]
    public function delete_estate_qna(Request $request) {

        $success = false;
        $msg = '';
        $data = [];

        $validator = Validator::make($request->all(), [
            'eid' => 'required',
            'did' => 'required',
        ]);

        if($validator->fails()) {
            $msg = "[eid, did],지정된 데이터를 모두 입력하세요.";
            return $this->jsonReturnResponse($success, $msg, $data);
        }

        try {

            DB::beginTransaction();
            $eid = $request->input('eid');
            $did = $request->input('did');

            if (UserEstateQuestion::admin_delete_content($eid, $did)) {
                UserEstateFiles::admin_delete_estate_all_files_remove($did, 1);
                DB::commit();
                $success = true;
            }


        } catch (\Exception $e) {
            $msg = 'Error. ';
            DB::rollBack();
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // [c]
    public function delete_withdraw(Request $request) {

        $success = false;
        $msg = '';
        $data = [];

        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if($validator->fails()) {
            $msg = "[id],지정된 데이터를 모두 입력하세요.";
            return $this->jsonReturnResponse($success, $msg, $data);
        }

        try {

            DB::beginTransaction();
            $id = $request->input('id');

            if (UserRequestWithDrawal::admin_delete_content($id)) {
                DB::commit();
                $success = true;
            }


        } catch (\Exception $e) {
            $msg = 'Error. ';
            DB::rollBack();
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // [c]
    public function delete_deposit(Request $request) {

        $success = false;
        $msg = '';
        $data = [];

        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if($validator->fails()) {
            $msg = "[id],지정된 데이터를 모두 입력하세요.";
            return $this->jsonReturnResponse($success, $msg, $data);
        }

        try {

            DB::beginTransaction();
            $id = $request->input('id');

            if (UserAssets::admin_delete_content($id)) {
                DB::commit();
                $success = true;
            }


        } catch (\Exception $e) {
            $msg = 'Error. ';
            DB::rollBack();
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }


    /* ---------------------------------------------------------------------------------- */
    // [c]
    public function search_user(Request $request) {

        $success = false;
        $msg = '';
        $data = [];

        try {

            $data['list'] = User::admin_list($request->input('keyword'));
            $data['survey_1'] = Survey::list(1);
            $data['survey_2'] = Survey::list(2);
            $data['survey_3'] = Survey::list(3);
            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error. ';
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // [c]
    public function search_leaved_user(Request $request) {

        $success = false;
        $msg = '';
        $data = [];

        try {

            $data['list'] = User::admin_leaved_list($request->input('keyword'));

            if(count($data['list']) > 0) {
                $data['survey'] = LeavedUser::admin_leaved_list();
            }

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error. Search leaved User';
            DB::rollBack();
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // [c]
    public function search_contest(Request $request) {

        $success = false;
        $msg = '';
        $data = [];

        try {

            $data['list'] = Contest::admin_list($request->input('keyword'));
            $data['code'] = [
                '1 : 공모예정',
                '2 : 공모중',
                '3 : 공모마감',
            ];

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error. ';
            DB::rollBack();
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // [c]
    public function search_dividend(Request $request) {

        $success = false;
        $msg = '';
        $data = [];

        try {

            $data['list'] = Contest::admin_dividend_list($request->input('keyword'));
            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error. ';
            DB::rollBack();
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // [c] 배당상세 - 투자자 추가 step1 : 추가할 회원 검색
    public function search_dividend_user(Request $request) {

        $success = false;
        $msg = '';
        $data = [];

        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if($validator->fails()) {
            $msg = "[name],지정된 데이터를 모두 입력하세요.";
            return $this->jsonReturnResponse($success, $msg, $data);
        }

        try {

            $name = $request->input('name');

            $data['user'] = User::admin_search_user_from_dividend_add_stock($name);

            $success = true;


        } catch (\Exception $e) {
            $msg = 'Error. ' ;
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // [c]
    public function search_detail_dividend(Request $request) {

        $success = false;
        $msg = '';
        $data = [];

        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if($validator->fails()) {
            $msg = "[id],지정된 데이터를 모두 입력하세요.";
            return $this->jsonReturnResponse($success, $msg, $data);
        }

        try {

            $id = $request->input('id');
            $time = $request->input('time');
            $keyword = $request->input('keyword');

            $data['stock_list'] = UserStock::admin_contest_stock_people_list($id, $time, $keyword);

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error. ';
        }

        return $this->jsonReturnResponse($success, $msg, $data);
    }

    // [c]
    public function search_estate(Request $request) {

        $success = false;
        $msg = '';
        $data = [];

        $validator = Validator::make($request->all(), [
            'type' => 'required',
        ]);

        if($validator->fails()) {
            $msg = "[type],지정된 데이터를 모두 입력하세요.";
            return $this->jsonReturnResponse($success, $msg, $data);
        }

        try {

            $type = $request->input('type');
            $keyword = $request->input('keyword');

            $data['list'] = UserEstate::admin_list($type, $keyword);
            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error. ';
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // [c]
    public function search_user_assets(Request $request) {

        $success = false;
        $msg = '';
        $data = [];

        try {

            $data['user_list'] = User::admin_user_list($request->input('keyword'));

            // 전세 투자금
            $data['user_total_sum_stock_estate'] = UserStock::admin_getUserEstateAmount();
            // 전세 예치금
            $data['user_total_have_estate_amount'] = UserEstate::admin_getUserEstateSumAmount();

            // 일반 투자금
            $data['user_total_sum_stock_amount'] = UserStock::admin_getUserAmount();
            // 일반 예치금
            $data['user_total_have_amount'] = UserAssets::admin_getUserTotalHaveAmount();

            // 총 수익금
            $data['user_total_sum_dividend'] = UserDividend::admin_getUserDividendSumAmount();

            $data['guide'] = '총 보유자산의 경우 전세, 일반 투자 및 예치금 데이터를 활용하여 합산합니다.';

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error. ';
            DB::rollBack();
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // [c]
    public function search_withdraw(Request $request) {

        $success = false;
        $msg = '';
        $data = [];

        try {

            $data['list'] = UserRequestWithDrawal::admin_list($request->input('keyword'));
            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error. ';
            DB::rollBack();
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }

    // [c]
    public function search_deposit(Request $request) {

        $success = false;
        $msg = '';
        $data = [];

        try {

            $data['list'] = UserAssets::admin_deposit_list($request->input('keyword'));
            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error. Search Deposit' .$e;
        }

        return $this->jsonReturnResponse($success, $msg, $data);

    }


    /* ---------------------------------------------------------------------------------- */


    // Common Return Json Response
    private function jsonReturnResponse($success, $msg, $data) { return response()->json([ 'success' => $success, 'msg' => $msg, 'data' => $data ]); }
}
