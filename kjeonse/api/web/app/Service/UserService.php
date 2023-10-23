<?php

namespace App\Service;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Certificate\SmsController;

class UserService {

    /*
     * Notice
     *
     * Refactoring Request SNS Auth Code
     * - join, find password, find email
     *
     * */

    private $fromUserPhone = '010-2191-4791';

    // [c] User Login
    public function Login($request) {

        $success = false;
        $msg     = '';
        $data    = [];

        try {

            $data = \App\Models\User::login($request['email'], $request['password']);
            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error : Update User Info Service';
        }

        return response()->json([ 'success' => $success, 'msg' => $msg, 'data' => $data ]);

    }


    // [c] User MyPage
    public function mypage() {

        $success = false;
        $msg     = '';
        $data    = [];

        try {

            $user_id = Auth::user()->id;

            $data['user'] = \App\Models\User::mypage($user_id);
            $data['leaved_list'] = \App\Models\LeavedReason::list();
            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error : Update User Info Service';
        }

        return response()->json([ 'success' => $success, 'msg' => $msg, 'data' => $data ]);

    }


    // [c] User Change Password
    public function chagePassword($request) {

        $success = false;
        $msg     = '';
        $data    = [];

        try {

            $id = Auth::user()->id;

            if(\App\Models\User::changePasswordInvalidCheck($id, $request['old_password'], $request['password'])) {
                \App\Models\User::changePassword($id, $request['password']);
                $success = true;
            } else {
                $msg = '잘못된 패스워드이거나 중복된 패스워드로 변경을 완료할 수 없습니다.';
            }

        } catch (\Exception $e) {
            $msg = 'Error : Change Password Service';
        }

        return response()->json([ 'success' => $success, 'msg' => $msg, 'data' => $data ]);

    }


    // [c] User Info Update
    public function userInfoUpdate($request) {

        $success = false;
        $msg     = '';
        $data    = [];

        try {

            $id = Auth::user()->id;

            \App\Models\User::updateUserInfo($id, $request['privacy_marketing']);
            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error : Update User Info Service';
        }

        return response()->json([ 'success' => $success, 'msg' => $msg, 'data' => $data ]);

    }


    // [ing] User Leave
    public function userLeave($request) {

        $success = false;
        $msg     = '';
        $data    = [];

        try {

            $user_id = Auth::user()->id;

            /* --------------------- */
            // Check User Program
            /* --------------------- */

            // User Table
            \App\Models\User::userLeave($user_id);

            // leaved_user Table
            \App\Models\LeavedUser::insertLeavedUser($user_id, $request['reason']);

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error : Leave User Service';
        }

        return response()->json([ 'success' => $success, 'msg' => $msg, 'data' => $data ]);

    }


    // [ing] 회원가입 작업 중
    public function join($user) {

        $success = false;
        $msg = '';
        $data = [];

        try {

            if(!empty(\App\Models\User::canUsingEmailCheck($user['email']))) {
                $msg = '이미 존재하는 Email 계정입니다.';
                return response()->json(['success' => $success, 'msg' => $msg, 'data' => $data]);
            }

            if(\App\Models\CertificateSms::joinSmsAuthCheck($user)) {

                $user_id = \App\Models\User::store(0, $user);

                if(!empty($user['survy'])) {
                    \App\Models\UserJoinSurvey::store($user_id, $user['survy']);
                }

            } else {
                $msg = '문자 인증이 진행되지 않았습니다.';
            }

            $success = true;

        } catch (\Exception $e) {
            $msg = $e;
        }

        return response()->json(['success' => $success, 'msg' => $msg, 'data' => $data]);

    }


    // [c] 로그인 유저 좋아요 공모전 리스트 포함 데이터
    public function contestList($request) {

        $success = false;
        $msg = '';
        $data = [];

        try {

            $user_id = Auth::user()->id;

            $keyword = $request['keyword'] ?? '';
            $type = $request['type'] ?? 0;

            $data['contest'] = \App\Models\Contest::loginUserContestList($type, $keyword, $user_id);
            $success = true;

        } catch (\Exception $e) {
            $msg = $e;
        }

        return response()->json(['success' => $success, 'msg' => $msg, 'data' => $data]);

    }


    // [c] 나의 전세 현황 리스트
    public function estateList($request) {

        $success = false;
        $msg = '';
        $data = [];

        try {

            $user_id = Auth::user()->id;

            $data['list'] = \App\Models\UserEstate::myEstateList($user_id);
            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error: User Estate List';
        }

        return response()->json(['success' => $success, 'msg' => $msg, 'data' => $data]);

    }


    // 나의 투자 현황 데이터
    public function stockMyInfo($request) {

        $success = false;
        $msg = '';
        $data = [];

        try {

            $user_id = Auth::user()->id;

            $data['stockedByToken'] = (int)\App\Models\UserStock::getStockedBuyTokenCount($user_id, $request['contest_id']);
            $data['can_use_amount'] = (int)\App\Models\UserAssets::getCanUseMyAmount($user_id);
            $data['can_stock_estate_my_list'] = \App\Models\UserEstate::getMyEstateList($user_id, $request['contest_id']);
            $data['stocked_estate_history'] = \App\Models\UserStock::getEstateStockedHistory($user_id, $request['contest_id']);
            $data['totalSumEstateAmount'] = (int)\App\Models\UserEstate::jeonse_total_assets($user_id) - (int)\App\Models\UserStock::getStockedEstateAmount($user_id) ;

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error: User Stock My Info '.$e;
        }

        return response()->json(['success' => $success, 'msg' => $msg, 'data' => $data]);

    }


    // 회원가입 인증 번호 발송
    public function sms($request) {

        $success = false;
        $msg     = '';
        $data    = [];

        try {

            $sms = new SmsController();

            $certificateNumber = rand(000000,999999);
            $sendToUserPhone = $request['phone'];

            // 하루동안 인증번호 발송 가능한 회수 체크
            $todaySendLimitCheck = \App\Models\CertificateSms::sendLimitCheck($request);

            // 사용하려는 계정과 휴대폰 번호로 인증이 가능한지 확인
            $usingPhoneEmailCanAuthCheck = \App\Models\CertificateSms::canUsingPhoneEmailCheck($request['email'], $request['phone']);

            if($todaySendLimitCheck < 4
                && $usingPhoneEmailCanAuthCheck)
            {
                \App\Models\CertificateSms::store($request, $certificateNumber, 0);
                $data['sms_code'] = $certificateNumber;
                $send_msg = 'K-Jeonse 회원가입 인증번호 : ' . $certificateNumber;
                // $sms->send($this->fromUserPhone, $sendToUserPhone, $send_msg);
                $data['invalid'] = true;
            } else {

                if(!$usingPhoneEmailCanAuthCheck) {
                    $msg = '입력하신 휴대폰 번호 또는 이메일은 사용 중으로 인증을 시도할 수 없습니다.';
                } else {
                    $msg = '하루 동안 가능한 인증 회수 3회를 초과하셨습니다.';
                }
                $data['invalid'] = false;
            }

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error : Join SMS Certificate Error Controller' . $e;
        }

        return response()->json([ 'success' => $success, 'msg' => $msg, 'data' => $data ]);

    }


    // 회원가입 인증 번호 확인 요청
    public function checkSms($request) {

        $success = false;
        $msg = '';
        $data = [];

        try {

            $res = \App\Models\CertificateSms::smsInvalidCheck($request, 0);

            if($res == 0) {
                $msg = '휴대폰 인증에 성공했습니다.';
                $data = 0;
            } else if($res == 1) {
                $msg = '인증번호를 재 확인해주세요.';
                $data = 1;
            } else if ($res == 2) {
                $msg = '인증번호 확인 회수 5회가 초과되었습니다.';
                $data = 2;
            } else if ($res == 3) {
                $msg = '심각. 시스템 우회가 감지되었습니다.';
                $data = 3;
            }

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error: User Join Sms Invalid Check '.$e;
        }

        return response()->json(['success' => $success, 'msg' => $msg, 'data' => $data]);

    }


    // 패스워드 찾기, 이메일로 확인
    public function findPassword($request) {

        $success = false;
        $msg = '';
        $data = [];

        try {

            if(!empty(\App\Models\User::findPasswordhasEmail($request['email']))) {
                $data = true;
            } {
                $data = false;
            }

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error: User find Password'.$e;
        }

        return response()->json(['success' => $success, 'msg' => $msg, 'data' => $data]);

    }


    // 패스워드 찾기 인증번호 발송 요청
    public function findPasswordCertificate($request) {

        $success = false;
        $msg     = '';
        $data    = [];

        try {

            $sms = new SmsController();

            $certificateNumber = rand(000000,999999);
            $sendToUserPhone = $request['phone'];

            // 하루동안 인증번호 발송 가능한 회수 체크
            $todaySendLimitCheck = \App\Models\CertificateSms::sendLimitCheck($request);

            // email, phone 정보를 이용하여 패스워드를 찾을 수 있는지 체크
            $usingPhoneEmailCanAuthCheck = \App\Models\CertificateSms::isHavePhoneEmailCheck($request['email'], $request['phone']);

            if($todaySendLimitCheck < 4
                && $usingPhoneEmailCanAuthCheck)
            {
                \App\Models\CertificateSms::store($request, $certificateNumber, 1);
                $data['sms_code'] = $certificateNumber;
                $send_msg = 'K-Jeonse 패스워드 찾기 인증번호 : ' . $certificateNumber;
                // $sms->send($this->fromUserPhone, $sendToUserPhone, $send_msg);
                $data['invalid'] = true;
            } else {

                if(!$usingPhoneEmailCanAuthCheck) {
                    $msg = '이메일 및 전화번호를 통해 계정 정보 확인이 불가합니다. 이메일 또는 전화번호를 확인해주세요.';
                } else {
                    $msg = '하루 동안 가능한 인증 회수 3회를 초과하셨습니다.';
                }
                $data['invalid'] = false;
            }

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error : Find Password Certificate' . $e;
        }

        return response()->json([ 'success' => $success, 'msg' => $msg, 'data' => $data ]);

    }


    // 패스워드 찾기 발송된 인증번호 확인 요청
    public function findPasswordCertificateCheck($request) {

        $success = false;
        $msg = '';
        $data = [];

        try {

            $res = \App\Models\CertificateSms::smsInvalidCheck($request, 1);

            if($res == 0) {
                $msg = '휴대폰 인증에 성공했습니다.';
                $data = 0;
            } else if($res == 1) {
                $msg = '인증번호를 재 확인해주세요.';
                $data = 1;
            } else if ($res == 2) {
                $msg = '인증번호 확인 회수 5회가 초과되었습니다.';
                $data = 2;
            } else if ($res == 3) {
                $msg = '심각. 시스템 우회가 감지되었습니다.';
                $data = 3;
            }

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error: User Join Sms Invalid Check '.$e;
        }

        return response()->json(['success' => $success, 'msg' => $msg, 'data' => $data]);

    }


    //
    public function findPasswordUpdate($request) {

        $success = false;
        $msg = '';
        $data = [];

        try {

            // 패스워드 찾기 문자 인증이 완료되었는지 체크
            if(\App\Models\CertificateSms::changePasswordAuthSmsCheck($request['email'], $request['phone'])) {

                if(\App\Models\User::updatePassword($request['email'], $request['phone'] ,$request['password'])) {
                    $data['code'] = 1;
                    $msg = '패스워드 변경이 완료되었습니다.';
                } else {
                    $data['code'] = 3;
                    $msg = '심각. 잘못된 접근입니다.';
                }

            } else {

                $data['code'] = 2;
                $msg = '패스워드 변경을 위해서는 문자 인증이 진행되어야 합니다.';

            }

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error: User find password complete Update Password '.$e;
        }

        return response()->json(['success' => $success, 'msg' => $msg, 'data' => $data]);

    }


    //
    public function userPhoneUpdateCertificate($request) {


        $success = false;
        $msg     = '';
        $data    = [];

        try {

            $sms = new SmsController();

            $certificateNumber = rand(000000,999999);
            $sendToUserPhone = $request['phone'];

            // 하루동안 인증번호 발송 가능한 회수 체크
            $todaySendLimitCheck = \App\Models\CertificateSms::sendLimitCheck($request);

            // email, phone 정보를 이용하여 패스워드를 찾을 수 있는지 체크
            $usingPhoneEmailCanAuthCheck = \App\Models\CertificateSms::canUsingChangePhoneNumber($request['phone']);

            if($todaySendLimitCheck < 4
                && $usingPhoneEmailCanAuthCheck)
            {
                \App\Models\CertificateSms::store($request, $certificateNumber, 3);
                $data['sms_code'] = $certificateNumber;
                $send_msg = 'K-Jeonse 전화번호 변경 인증번호 : ' . $certificateNumber;
                // $sms->send($this->fromUserPhone, $sendToUserPhone, $send_msg);
                $data['invalid'] = true;
            } else {

                if(!$usingPhoneEmailCanAuthCheck) {
                    $msg = '사용 중에 있는 전화번호 입니다. 전화번호를 재 확인해주세요.';
                } else {
                    $msg = '하루 동안 가능한 인증 회수 3회를 초과하셨습니다.';
                }
                $data['invalid'] = false;
            }

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error : Find Password Certificate' . $e;
        }

        return response()->json([ 'success' => $success, 'msg' => $msg, 'data' => $data ]);

    }


    //
    public function userPhoneUpdateCertificateCheck($request) {

        $success = false;
        $msg = '';
        $data = [];

        try {

            $res = \App\Models\CertificateSms::smsInvalidCheck($request, 3);

            if($res == 0) {

                $user_id = Auth::user()->id;
                \App\Models\User::updateUserPhoneNumber($user_id, $request['email'], $request['phone']);

                $msg = '휴대폰 인증에 성공했습니다.';
                $data = 0;

            } else if($res == 1) {
                $msg = '인증번호를 재 확인해주세요.';
                $data = 1;
            } else if ($res == 2) {
                $msg = '인증번호 확인 회수 5회가 초과되었습니다.';
                $data = 2;
            } else if ($res == 3) {
                $msg = '심각. 시스템 우회가 감지되었습니다.';
                $data = 3;
            }

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error: User Join Sms Invalid Check '.$e;
        }

        return response()->json(['success' => $success, 'msg' => $msg, 'data' => $data]);

    }


    //
    public function findAccountSendingSmsAuthCode($request) {

        $success = false;
        $msg     = '';
        $data    = [];

        try {

            $sms = new SmsController();

            $certificateNumber = rand(000000,999999);
            $sendToUserPhone = $request['phone'];

            // 하루동안 인증번호 발송 가능한 회수 체크
            $todaySendLimitCheck = \App\Models\CertificateSms::sendLimitCheck($request);

            // email, phone 정보를 이용하여 패스워드를 찾을 수 있는지 체크
            $canUsingPhoneFindToEmail = \App\Models\CertificateSms::findAccount($request['phone']);

            if($todaySendLimitCheck < 4
                && $canUsingPhoneFindToEmail)
            {
                \App\Models\CertificateSms::store($request, $certificateNumber, 2);
                $data['sms_code'] = $certificateNumber;
                $send_msg = 'K-Jeonse 계정 찾기 인증번호 : ' . $certificateNumber;
                // $sms->send($this->fromUserPhone, $sendToUserPhone, $send_msg);
                $data['invalid'] = true;

            } else {

                if(!$canUsingPhoneFindToEmail) {
                    $msg = '해당 번호로 계정을 찾을 수 없습니다. 핸드폰 번호를 재 확인해주세요.';
                } else {
                    $msg = '하루 동안 가능한 인증 회수 3회를 초과하셨습니다.';
                }

                $data['invalid'] = false;
            }

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error : User Find Account Certificate ' . $e;
        }

        return response()->json([ 'success' => $success, 'msg' => $msg, 'data' => $data ]);

    }


    //
    public function findAccountSendingSmsAuthCodeCheck($request) {

        $success = false;
        $msg = '';
        $data = [];

        try {

            $res = \App\Models\CertificateSms::smsInvalidCheck($request, 2);

            if($res == 0) {

                $data['info'] = \App\Models\User::findReturnUserNameAndEmail($request['phone']);

                $msg = '휴대폰 인증에 성공했습니다.';
                $data['code'] = 0;

            } else if($res == 1) {

                $msg = '인증번호를 재 확인해주세요.';
                $data['code'] = 1;

            } else if ($res == 2) {

                $msg = '인증번호 확인 회수 5회가 초과되었습니다.';
                $data['code'] = 2;

            } else if ($res == 3) {

                $msg = '심각. 시스템 우회가 감지되었습니다.';
                $data['code'] = 3;

            }

            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error: User find Account Invalid Check '.$e;
        }

        return response()->json(['success' => $success, 'msg' => $msg, 'data' => $data]);

    }


    //
    public function addInfo($request) {

        $success = false;
        $msg = '';
        $data = [];

        try {
            $user_id = null;

            if($user_id = \App\Models\User::socialLoginAddInfo($request)) {
                \App\Models\UserJoinSurvey::store($user_id, $request['survy']);
                $user['id'] = $request['id'];
                $data = \App\Models\User::snsLogin($user, $request['type']);

                $success = true;
            }

        } catch (\Exception $e) {
            $msg = 'Error : socials login . '.$e;
        }

        return response()->json(['success' => $success, 'msg' => $msg, 'data' => $data]);
    }


    //
    public function mailing($request) {


        $success = false;
        $msg = '';
        $data = [];

        try {

            $user_id = \App\Models\User::mailing($request);

            if(!empty($user_id)) {

                if(!empty($request['survy'])) {
                    \App\Models\UserJoinSurvey::store($user_id, $request['survy']);
                }

                $success = true;
            }

        } catch (\Exception $e) {
            $msg = 'Error: Request Mailing '.$e;
        }

        return response()->json(['success' => $success, 'msg' => $msg, 'data' => $data]);

    }
}
