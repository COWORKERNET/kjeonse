<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{

    use HasApiTokens, HasFactory, Notifiable;


    //
    protected function snsLogin($user, $type) {

        $loginType = 0;

        if($type == "kakao")        { $loginType = 1; }
        else if ($type == "naver")  { $loginType = 2; }
        else if ($type == "google") { $loginType = 3; }
        else {
            // apple
            $loginType = 4;
        }

        $user_id = $user['id'];

        $data = [];

        $res = $this->where('type', $loginType)->where('sns_id', $user['id'])->first();

        if(!empty($res->email)) {

            // success login
            $data['code'] = 1;
            $data['token'] = $this->createToken($res['email'], 'laravel_socials_password');

        } else {

            if(empty($res->email)) {
                $data['code'] = 2;
            } else {
                $data['code'] = 1;
            }

            if(empty($res)) {
                $this->insertGetId([
                    'type' => $loginType,
                    'sns_id' => $user_id,
                ]);
            }
        }

        return $data;
    }

    //
    protected function socialLoginAddInfo($request) {

        $id = $request['id'];
        $type = $request['type'];
        $email = $request['email'];
        $birth = $request['birth'];
        $name = $request['name'];
        $phone = $request['phone'];
        $privacy_agree = $request['privacy_agree'];
        $privacy_marketing = $request['privacy_marketing'];

        $loginType = 0;

        if($type == "kakao")        { $loginType = 1; }
        else if ($type == "naver")  { $loginType = 2; }
        else if ($type == "google") { $loginType = 3; }
        else {
            // apple
            $loginType = 4;
        }

        $res = $this->where('type', $loginType)->where('type', '!=', 0)->where('sns_id', $id);

        $user = $res->select('id')->first();

        $res->update([
            'email' => $email,
            'password' => bcrypt('laravel_socials_password'),
            'name' => $name,
            'phone' => $phone,
            'birth' => $birth,
            'privacy_agree' => $privacy_agree,
            'privacy_info' => $privacy_agree,
            'privacy_marketing' => $privacy_marketing,
        ]);

        return $user['id'];

    }


    //
    protected function register_sns($user, $type) {

        $data = [];
        $loginType = 0;
        $user_id = null;

        switch ($type) {
            case "kakao":
                $loginType = 1;
                $user_id = $user['id'];
                break;

            case "naver":
                $loginType = 2;
                $user_id = $user['id'];
                break;

            case "google":
                $loginType = 3;
                $user_id = $user['id'];
                break;

            case "apple":
                $loginType = 4;
                $user_id = $user['id'];
                break;
        }

        $res = $this->where('type', $loginType)->where('email', $user['id'])->first();

        if(empty($res)) {

            $this->insert([
                'type' => $loginType,
                'email' => $user['id'],
                'password' => bcrypt('1234'),
            ]);

            $data['code'] = 1;
            $data['token'] = $this->createToken($user['id'], '1234');


        } else {

            $data['code'] = 1;
            $data['token'] = $this->createToken($res['id'], '1234');

        }

        return $data;
    }

    // User Login 임대인, 임차인
    protected function login($user_email, $user_password) {

        $data = [];
        $msg = "";
        $success = false;

        // User Check
        $res = $this->select('id', 'name', 'status', 'type', 'password', 'email')->where('email', $user_email)->first();

        if($res) {
            if(password_verify($user_password, $res->password)) {
                try {
                    if($res->status != 1) {
                        $data['name'] = $res['name'];
                        $data['token'] = $this->createToken($user_email, $user_password);
                    } else {
                        $msg = "탈퇴 처리된 회원입니다.";
                    }
                    $success = true;
                } catch (\Exception $e) {
                    $msg = "일시적인 오류가 발생했습니다. [ token error ]";
                }
            } else {
                $msg = "아이디 혹은 패스워드 정보가 올바르지 않습니다.";
            }
        } else {
            $msg = "아이디 혹은 패스워드 정보가 올바르지 않습니다.";
        }

        return response()->json([ 'success' => $success, 'msg' => $msg, 'data' => $data ]);

    }


    // User Mypage
    protected function mypage($user_id) {
        return $this->select(
            'email',
            'phone',
            'name',
            'birth',
            'privacy_info',
            'privacy_marketing'
        )->where('status', 0)->where('id', $user_id)->first();
    }


    // User Change Password
    protected function changePassword($user_id, $user_password) {
        return $this->where('id', $user_id)->update(['password' => bcrypt($user_password)]);
    }


    // User Change Old Password Invalid Check
    protected function changePasswordInvalidCheck($user_id, $user_old_password, $user_password) {
        // 기존 패스워드와 변경될 패스워드는 동일할 수 없다.
        $res = $this->select('password')->where('id', $user_id)->first();
        if(
            password_verify($user_old_password, $res->password) &&
            !password_verify($user_password, $res->password)
        ) {
            return true;
        }
        return false;
    }


    // 회원 정보 변경
    protected function updateUserInfo($user_id, $user_privacy_marketing) {
        //
        return $this->where('id', $user_id)->update([
            'privacy_marketing' => $user_privacy_marketing ? 0 : 1,
        ]);
    }


    // 회원 탈퇴
    protected function userLeave($user_id) {
        //
        return $this->where('id', $user_id)->update([
            'status' => 1,
            'leaved_at' => now(),
        ]);
    }


    // join 유저 회원가입
    protected function store($type, $user) {

        return $this->insertGetId([
            'type'              => $type,
            'email'             => $user['email'],
            'password'          => bcrypt($user['password'] ?? null),
            'name'              => $user['name'],
            'birth'             => $user['birth'],
            'phone'             => $user['phone'],
            'privacy_agree'     => $user['privacy_agree'],
            'privacy_info'      => $user['privacy_agree'],
            'privacy_marketing' => $user['privacy_marketing'],
        ]);

    }


    //
    protected function canUsingEmailCheck($email) {
        return $this->where('email', $email)->count();
    }


    //
    protected function findPhoneCheck($phone) {
        return $this->where('phone', $phone)->count();
    }


    //
    protected function findPasswordhasEmailAndPhone($email, $phone) {
        return $this->where('email', $email)->where('phone', $phone)->count();
    }


    //
    protected function changeUserPhoneNumberSoNumberCheck($phone) {
        // 사용자가 변경하고자 하는 전화번호가 데이터베이스 상에 존재하지 않는 경우 True

        return $this->where('phone', $phone)->first();
    }


    //
    protected function updatePassword($email, $phone, $changePassword) {

        $invalidCheck = false;

        $res = $this->where('email', $email)->where('phone', $phone);

        if($res->count()) {
            $res->update([
                'password' => bcrypt($changePassword),
            ]);

            $invalidCheck = true;

        } else {

            // 인증까지 완료한 이후, 비정상적인 방법으로 다른 계정의 패스워드를 변경하려는 경우 모든 인증번호의 용도를 말소한다.
            \App\Models\CertificateSms::authSmsCancellation($email, $phone, 1);

        }

        return $invalidCheck;
    }


    //
    protected function updateUserPhoneNumber($user_id, $email, $phone) {
        $this->where('id', $user_id)->where('email', $email)->update([
            'phone' => $phone,
        ]);
    }


    //
    protected function findAccount($phone) { return $this->where('phone', $phone)->count(); }


    //
    protected function findReturnUserNameAndEmail($phone) {
        return $this->select(
            'name',
            'email',
        )->where('phone', $phone)->first();
    }


    //
    protected function mailing($request) {
        return $this->insertGetId([
            'type' => 5,
            'email' => $request['email'],
            'privacy_agree' => $request['agree'],
            'privacy_marketing' => $request['marketing']
        ]);
    }


    /* --------------------------------------------------------------------- */
    // List(in, out), Search, Excel Download, Delete
    protected function admin_list($keyword = '') {

        $res = $this->select(
            'id',
            'type',
            'name',
            'email',
            'birth',
            'phone',
            'privacy_info',
            'privacy_marketing',
            'created_at',
            'leaved_at',
        )
            ->where('status', 0)
            ->where('id', '!=', 1);

        if(!empty($keyword)) {
            $res->where('name', 'like', '%'.$keyword.'%');
            $res->orWhere('phone', 'like', '%'.$keyword.'%');
        }

        $res->groupBy('id');

        return $res->get();


    }


    // [c]
    protected function admin_leaved_list($keyword = '') {

        $res = $this->leftJoin('leaved_user', 'leaved_user.ref_user_id', '=', 'users.id')
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'users.birth',
                'users.phone',
                'users.created_at',
                'users.leaved_at'
        )
            ->where('users.status', 1)
            ->where('leaved_user.status', 0);

        if(!empty($keyword)) {
            $res->where('users.name', 'like', '%'.$keyword.'%');
        }

        return $res->groupBy('id')->get();
    }


    //
    protected function admin_user_list($keyword = null) {
        $res = $this->select(
            'id',
            'name',
            'phone',
        )
            ->where('status', 0)
            ->where('id', '!=', 1);

        if(!empty($keyword)) {
            $res->where('name', 'like', '%'.$keyword.'%');
        }

        return $res->get();
    }


    //
    protected function admin_user_leaved($id) {
        return $this->where('id', $id)->update([
            'status' => 1,
            'leaved_at' => now(),
        ]);
    }


    //
    protected function admin_search_user_from_dividend_add_stock($name) {
        return $this->select(
            'id',
            'type',
            'email',
            'name',
            'phone',
        )
            ->where('status', 0)
            ->where('type', '!=', 5)
            ->where('name', 'like', '%'.$name.'%')
            ->get();
    }


    // Create Token
    private function createToken($email, $password) {
        // access_token, refresh_token
        $response = Http::asForm()->post('https://api.kjeonse.com/oauth/token', [
            'grant_type' => 'password',
            'client_id' => '2',
            'client_secret' => 'SDT4RBnTlr7TFgS56zoHvO2lS8cvZnqAyGKG0JWw',
            'username' => $email,
            'password' => $password,
            'scope' => '*',
        ]);
        return $response->json();
    }


    // Refresh Token
    public function refreshToken(Request $request) {

        $data = $request->only('refresh_token');

        $response = Http::asForm()->post('https://api.kjeonse.com/oauth/token', [
            'grant_type' => 'refresh_token',
            'client_id' => '2',
            'client_secret' => 'SDT4RBnTlr7TFgS56zoHvO2lS8cvZnqAyGKG0JWw',
            'refresh_token' => $data['refresh_token'],
            'scope' => '*',
        ]);
        return $response->json();
    }
}
