<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificateSms extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'certificate_sms';


    //
    protected function store($request, $certificateNumber, $type) {

        $timestamp = strtotime("+3 minutes");
        $email = !empty($request['email']) ? $request['email'] : '';
        $phone = $request['phone'];

        $this->where('email', $email)
             ->where('phone', $phone)
             ->update([
                 'status' => 1,
             ]);

        $this->insert([
            'email' => $email,
            'phone' => $phone,
            'sms' => $certificateNumber,
            'type' => $type,
            'auth_invalid_at' => date("Y-m-d H:i:s", $timestamp)
        ]);

    }


    // 하루 최대 보낼 수 있는 인증 번호 회수 리턴
    protected function sendLimitCheck ($request) {

        return $this->where('phone', $request['phone'])
            ->whereNull('authenticated_at')
            ->where('created_at', '>', date('Y-m-d'))
            ->count();

    }


    // 사용 가능한 이메일, 핸드폰인지 확인 함수
    protected function canUsingPhoneEmailCheck($email, $phone) {

        $isEmptyUsingPhoneCheck = \App\Models\User::findPhoneCheck($phone);
        $isEmptyUsingEmailCheck = \App\Models\User::canUsingEmailCheck($email);

        $res = false;
        if(!$isEmptyUsingPhoneCheck && !$isEmptyUsingEmailCheck) {
            $res = true;
        }

        return $res;
    }


    // 계정 정보 찾기 기능 : 입력한 이메일 및 휴대폰 번호 데이터가 존재하는지 체크
    protected function isHavePhoneEmailCheck($email, $phone) {

        $res = false;

        if(\App\Models\User::findPasswordhasEmailAndPhone($email, $phone)) {
            $res = true;
        }

        return $res;
    }


    // 변경 가능한 전화번호인지 체크
    protected function canUsingChangePhoneNumber($phone) {

        $res = false;

        if(empty(\App\Models\User::changeUserPhoneNumberSoNumberCheck($phone))) {
            $res = true;
        }

        return $res;
    }


    /*
     * 0 : 휴대폰 인증에 성공했습니다.
     * 1 : 인증번호를 재 확인해주세요.
     * 2 : 인증번호 확인 회수 5회가 초과되었습니다.
     * 3 : 심각. 시스템 우회가 감지되었습니다.
    */
    protected function smsInvalidCheck($request, $type) {

        $email = !empty($request['email']) ? $request['email'] : '';
        $phone = $request['phone'];
        $code = $request['code'];

        $res = $this->where('phone', $phone)
                    ->where('type', $type)
                    ->where('status', 0);

        if(!empty($email)) {
            $res->where('email', $email);
        }

        if($res->count()) {

            if($res->first()->auth_count > 5) {
                $res->update([
                    'status' => 1,
                ]);
                return 2;
            }

            $res->increment('auth_count', 1);

            $minuteCheck = $res->where('sms', $code)->first();

            if(empty($minuteCheck)) {
                return 1;
            }

            // 3분 이내 인증 번호 체크했는지 확인
            $timestamp = strtotime("-3 minutes");

            if($minuteCheck->auth_invalid_at < date("Y-m-d H:i:s", $timestamp)) {

                $this->where('id', $minuteCheck->id)->update(['status' => 1]);
                return 3;
            }

            $res->update([
                'authenticated_at' => now(),
            ]);

            return 0;

        }

        return 1;
    }


    // 회원가입 시, 문자 인증을 진행했는지 체크
    protected function joinSmsAuthCheck($user) {
        return $this->where('status', 0)
            ->where('email', $user['email'])
            ->where('phone', $user['phone'])
            ->whereNotNull('authenticated_at')
            ->count();
    }


    //
    protected function changePasswordAuthSmsCheck($email, $phone) {
        return $this->where('status', 0)
            ->where('type', 1)
            ->where('email', $email)
            ->where('phone', $phone)
            ->whereNotNull('authenticated_at')
            ->where('authenticated_at', '>', date('Y-m-d'))
            ->count();
    }


    // 인증번호 말소 처리
    protected function authSmsCancellation($email, $phone, $type) {
        return $this->where('status', 0)
            ->where('type', $type)
            ->where('email', $email)
            ->where('phone', $phone)
            ->update([
                'status' => 1,
            ]);
    }


    //
    protected function findAccount($phone) {

        $res = false;

        if(!empty(\App\Models\User::findAccount($phone))) {
            $res = true;
        }

        return $res;
    }

    //
    protected function serializeDate(\DateTimeInterface $date): string { return $date->format('Y-m-d H:i:s'); }
}
