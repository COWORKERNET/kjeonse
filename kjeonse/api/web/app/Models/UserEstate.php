<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserEstate extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'user_estate_register_list';

    protected $fillable = [
        'ref_admin_id',
    ];

    // 전세 부동산 관리 전세 현황 리스트
    protected function myEstateList($user_id) {

        return $this->select(
            DB::raw('(CASE WHEN (ref_user_id_type_lessor = '. $user_id . ') THEN 1 else 0 end) as isLessor'),
            'id',
            'title',
            'address',
            'address_detail',
            'admin_approve',
            DB::raw('(CASE WHEN (ref_user_id_type_lessor = '. $user_id . ') THEN lessor_name else lessee_name end) as name'),
            DB::raw('(CASE WHEN (ref_user_id_type_lessor = '. $user_id . ') THEN lessor_phone else lessee_phone end) as phone'),
            'password',
            'contractor_created_at',
            'contractor_open_at',
            'contractor_closed_at',
            'contractor_total_amount',
            'contractor_payment',
            'contractor_payment_at',
            'contractor_interim',
            'contractor_interim_at',
            'contractor_balance',
            'contractor_balance_at',
        )
            ->where('status', 0)
            ->where('ref_user_id_type_lessor', '=', $user_id)
            ->orWhere('ref_user_id_type_lessee', '=', $user_id)
            ->get();

    }


    // 등록된 전세 카운트
    protected function check($user_id) {
        return $this->where('status', 0)
            ->where('ref_user_id_type_lessor', $user_id)
            ->orWhere('ref_user_id_type_lessee', $user_id)
            ->count();
    }


    // 대시보드
    protected function list($user_id) {

        /*
         * 'ref_user_id_type_lessor',
           'ref_user_id_type_lessee',

        */
        return $this->select(
            DB::raw('(CASE WHEN (ref_user_id_type_lessor = '. $user_id . ') THEN 1 else 0 end) as isLessor'),
                'title',
                'address',
                'address_detail',
                'contractor_open_at',
                'contractor_closed_at',
                'contractor_total_amount',
                'contractor_payment',
                'contractor_interim',
                'contractor_balance',
            )
            ->where('status', 0)
            ->where('admin_approve', 1)
            ->where('ref_user_id_type_lessor', '=', $user_id)
            ->orWhere('ref_user_id_type_lessee', '=', $user_id)
            ->limit(2)
            ->get();
    }


    // 등록한 전세 자금 총합산
    protected function jeonse_total_assets($user_id) {

        $res = $this->select('contractor_total_amount')
            ->where('status', 0)->where('admin_approve', 1)->where('ref_user_id_type_lessor', $user_id)
            ->get();

        $total = 0;
        if(empty($res)) return $total;

        foreach ($res as $jeonse) {
            $total += ($jeonse->contractor_total_amount);
        }

        return $total;
    }


    // 사용자 등록한 전세 개수
    protected function userRegisterLessorCount($user_id) {
        return $this->where('status', 0)->where('admin_approve', 1)->where('ref_user_id_type_lessor', $user_id)->count();
    }


    // 전세 제목 가져오기 함수
    protected function getEstateTitle($id) {
        return $this->select('title')->where('status', 0)->where('id', $id)->first();
    }


    //
    protected function getIsMyEstateCheck($id, $user_id) {

        $res = $this->where('id', $id)->where('ref_user_id_type_lessor', $user_id)->first();

        return !empty($res);
    }


    // 전세 부동산 관리 : 임대차 계약 관리 '작성자' Type 설정을 위한 함수
    protected function getTypeUser($id, $user_id) {

        $res = $this->select(
            'ref_user_id_type_lessor',
            'ref_user_id_type_lessee',
        )->where('id', $id)->first();

        $data = null;

        if((int)$res->ref_user_id_type_lessor == $user_id) {
            return $data = 0;
        }

        if((int)$res->ref_user_id_type_lessee == $user_id) {
            return $data = 1;
        }

        if(empty($data)) {
            return $data = 2;
        }

    }


    // 전세 부동산 관리 : 문서 작성 시, 관계자 외 접근 확인
    protected function estatePolicyCheck($id, $user_id) {

        $data = false;

        $res = $this->select(
            'ref_user_id_type_lessor',
            'ref_user_id_type_lessee',
        )->where('status', 0)->where('id', $id)->first();

        if((int)$res->ref_user_id_type_lessor == $user_id
            || (int)$res->ref_user_id_type_lessee == $user_id) {
            $data = true;
        }

        return $data;
    }


    // 임대인 등록
    protected function registerLessor($user_id, $data) {

        $file = $data['file'];

        $originFileName = $file->getClientOriginalName();
        $fileSize = $file->getSize();
        $fileName = (now()->timestamp) . '_' . $originFileName;
        $path = $file->storeAs('public/estate/lessor/', $fileName);
        $url = "https://api.kjeonse.com/storage/estate/lessor/";

        $payment_at = !empty($data['payment_at']) ? $data['payment_at'] : null;
        $interim_at = !empty($data['interim_at']) ? $data['interim_at'] : null;
        $balance_at = !empty($data['balance_at']) ? $data['balance_at'] : null;
        $create_at  = !empty($data['contractor_create_at']) ? $data['contractor_create_at'] : null;

        $payment = !empty($data['payment']) ? $data['payment'] : 0;
        $interim = !empty($data['interim']) ? $data['interim'] : 0;
        $balance = !empty($data['balance']) ? $data['balance'] : 0;

        return $this->insertGetId([

            'ref_user_id_type_lessor' => $user_id,
            'password'       => $data['password'],
            'lessor_name'    => $data['lessor_name'],
            'lessor_phone'   => $data['lessor_phone'],
            'title'          => $data['title'],
            'post_code'      => $data['postCode'],
            'address'        => $data['address'],
            'address_detail' => $data['address_detail'],

            'contractor_open_at'   => $data['open_at'],
            'contractor_closed_at' => $data['close_at'],

            'contractor_created_at' => $create_at,
            'contractor_payment_at' => $payment_at,
            'contractor_interim_at' => $interim_at,
            'contractor_balance_at' => $balance_at,

            'contractor_payment' => $payment,
            'contractor_interim' => $interim,
            'contractor_balance' => $balance,

            'contractor_total_amount' => $data['total_payment'],

            'size'              => $fileSize,
            'originFileName'    => $originFileName,
            'fileAddress'       => $url.$fileName,
        ]);

    }


    // 임차인 등록
    protected function registerLessee($user_id, $data) {

        $success = false;
        $res = $this->where('id', $data['id'])
                    ->first();

        if(!empty($res) && empty($res->ref_user_id_type_lessee)) {

            if ($data['password'] == $res->password) {

                $this->where('status', 0)->where('ref_user_id_type_lessor', $res->ref_user_id_type_lessor)->update([
                    'ref_user_id_type_lessee' => $user_id,
                    'lessee_name' => $data['name'],
                    'lessee_phone' => $data['phone'],
                ]);

                $success = true;
            }
        }

        return $success;
    }


    // 나의 전세 현황 리스트 조회 함수
    protected function getMyEstateList($user_id) {
        return $this->select(
            'id',
            'title',
            'contractor_total_amount',
            'contractor_payment',
            'contractor_interim',
            'contractor_balance'
        )
            ->where('status', 0)
            ->where('admin_approve', 1)
            ->where('ref_user_id_type_lessor', $user_id)
            //->orWhere('ref_user_id_type_lessee', $user_id)
            ->get();
    }


    // 전세자금을 이용한 투자 이력 리스트 조회 함수
    protected function getStockedEstateHistory($user_id, $contest_id) {
        return $this->leftJoin('user_stock', 'user_stock.ref_user_lessor_id', '=', 'user_estate_register_list.id')
                    ->select(
                        'user_estate_register_list.id',
                        'user_estate_register_list.title',

                        DB::raw(
                            '(
                                select SUM(user_stock.amount)
                                from user_stock
                                where type = 1
                                and ref_user_lessor_id = user_estate_register_list.id
                                and ref_contest_id = ' . $contest_id . '
                                and ref_user_id = ' . $user_id .
                            ') as using_stocked_estate_amount'
                        ),

                        'contractor_payment',
                        'contractor_interim',
                        'contractor_balance',
                    )
                    ->where('user_estate_register_list.status', 0)
                    ->where('user_estate_register_list.admin_approve', 1)
                    ->where('user_estate_register_list.ref_user_id_type_lessor', $user_id)
                    ->distinct()
                    ->get();
    }


    // 투자 금액이 보유한 자산 금액 내 속하는지 체크하는 함수
    protected function stockOverCheck($user_id, $stockAmount, $estate_id) {

        // 선택된 전세 ID가 등록한 유저 소유인지 체크
        $estate = $this->select(
            'contractor_total_amount',
            'contractor_payment',
            'contractor_interim',
            'contractor_balance',
        )
            ->where('status', 0)
            ->where('admin_approve', 1)
            ->where('id', $estate_id)
            ->where('ref_user_id_type_lessor', $user_id)
            ->first();

        $estate_contractor_amount = 0;
        // 해당 전세 자금 전체 합산
        if($estate) {
            $estate_contractor_amount = (int)$estate->contractor_total_amount;
        }

        // 해당 전세 자금으로 투자된 금액 확인
        $estate_stocked_amount = (int)\App\Models\UserStock::getStockedEstateTargetAmount($user_id, $estate_id) || 0;

        $isInvalidCheck = false;

        // 특정 전세 자금으로 투자 시, 해당 전세 자금으로 투자현황과 투자할 금액 합산이 전세 자금을 초과하는지 체크
        if($estate_contractor_amount - ($estate_stocked_amount + $stockAmount) >= 0) {
            $isInvalidCheck = true;
        }

        return $isInvalidCheck;
    }


    // 임차인 전세 등록 시, 주소 검색 API
    protected function search($address) {

        $word = str_replace(' ', '', $address);

        return $this->select(
            'id',
            'post_code',
            'address',
            'address_detail',
            'lessor_name',
            'lessor_phone',
        )
            ->where('status', 0)
            ->where(DB::raw("REPLACE(address, ' ', '')"), 'like', '%'.$word.'%')
            ->groupBy('id')
            ->get();
    }


    /* -------------------------------------------------------------------------------- */
    // [c]
    protected function admin_list($type = null, $keyword = null) {

        $res = $this->select(
            'id',
            'status',
            'title',
            'lessor_name',
            'lessor_phone',
            'ref_user_id_type_lessee as isMatching',
            'address',
            'address_detail',
            'admin_approve',
            'contractor_created_at',
            'contractor_open_at',
            'contractor_closed_at'
        );

        $res->where('status', 0);

        if($type == 0) {
            $res->where('lessor_name', 'like', '%'.$keyword.'%');
            $res->orWhere('address', 'like', '%'.$keyword.'%');
        }

        if(!empty($keyword) && $type == 1) {
            $res->where('lessor_name', 'like', '%'.$keyword.'%');
        }

        if(!empty($keyword) && $type == 2) {
            $res->where('address', 'like', '%'.$keyword.'%');
        }

        $res->groupBy('id');

        return $res->where('status', 0)->get();
    }

    // [c]
    protected function admin_detail_contest($contest_id) {

        return $this->select(
            'title',
            'contractor_total_amount',
            'contractor_open_at',
            'contractor_closed_at',
            'contractor_created_at',
            'contractor_payment',
            'contractor_payment_at',
            'contractor_interim',
            'contractor_interim_at',
            'contractor_balance',
            'contractor_balance_at',
            'admin_approve as approve',
            'post_code',
            'address',
            'address_detail',
            'lessor_name',
            'lessor_phone',
            'lessee_name',
            'lessee_phone',
            'password',
            'originFileName',
            'fileAddress',
        )
            ->where('id', $contest_id)
            ->first();

    }

    // [c]
    protected function admin_update_content($data, $admin_id) {

        $res = $this->where('id', $data['eid'])->first();

        if(!empty($data['file'])) {

            $file = $data['file'];

            $originFileName = $file->getClientOriginalName();
            $fileSize = $file->getSize();
            $fileName = (now()->timestamp) . '_' . $originFileName;
            $path = $file->storeAs('public/estate/', $fileName);
            $url = "https://api.kjeonse.com/storage/estate/";

            $res->size = $fileSize;
            $res->originFileName = $originFileName;
            $res->fileAddress = $url.$fileName;
            $res->save();
        }

        return $res->where('id', $data['eid'])->update([

            'ref_admin_id'              => $admin_id,
            'title'                     => $data['title'],
            'contractor_total_amount'   => $data['contractor_total_amount'],
            'contractor_open_at'        => $data['contractor_open_at'],
            'contractor_closed_at'      => $data['contractor_closed_at'],
            'admin_approve'             => $data['approve'],
            'post_code'                 => $data['post_code'],
            'address'                   => $data['address'],
            'lessor_name'               => $data['lessor_name'],
            'lessor_phone'              => $data['lessor_phone'],
            'password'                  => $data['password'],

            'contractor_created_at'     => $data['contractor_created_at']   ?? null,
            'contractor_payment'        => $data['contractor_payment']      ?? null,
            'contractor_payment_at'     => $data['contractor_payment_at']   ?? null,
            'contractor_interim'        => $data['contractor_interim']      ?? null,
            'contractor_interim_at'     => $data['contractor_interim_at']   ?? null,
            'contractor_balance'        => $data['contractor_balance']      ?? null,
            'contractor_balance_at'     => $data['contractor_balance_at']   ?? null,
            'address_detail'            => $data['address_detail']          ?? null,

            'lessee_name'               => $data['lessee_name']  ?? null,
            'lessee_phone'              => $data['lessee_phone'] ?? null,

        ]);

    }

    // [..ing] 회원자산 관리 테이블 '전세예치금' 컬럼
    protected function admin_getUserEstateSumAmount() {
        return $this->join(
            'users', 'users.id', '=', 'user_estate_register_list.ref_user_id_type_lessor'
        )->select(
            'user_estate_register_list.ref_user_id_type_lessor as user_id',
            DB::raw(
                '(
                    select SUM(contractor_total_amount)
                    from user_estate_register_list
                    where status = 0 and admin_approve = 1 and ref_user_id_type_lessor = users.id
                ) as totalSumAmount'
            )
        )
            ->where('users.id', '!=', 1)
            ->groupBy('user_estate_register_list.ref_user_id_type_lessor')
            ->get();
    }

    // [ ...ing ] 삭제에 대한 상세 정책 수립 필요
    protected function admin_delete_content($estate_id) {
        return $this->where('id', $estate_id)->update([
            'status' => 1
        ]);
    }

    /* -------------------------------------------------------------------------------------------------- */


    //
    protected function serializeDate(\DateTimeInterface $date): string { return $date->format('Y-m-d H:i:s'); }
}
