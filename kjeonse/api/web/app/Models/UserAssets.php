<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/*
 * @defined = 개인 자산 관리 테이블
 *
 */
class UserAssets extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'user_assets';


    // User Dashboard Stock Section
    protected function userStockInfo($user_id) {

        $data = [];

        // 예치금
        $total_withDraw_charge_amount     = $this->chargeTotalWithDraw($user_id);               // 충전된 전체 예치금
        $total_withDraw_stock_used_amount = $this->usedTotalWithDraw($user_id);                 // 사용된 예치금 전체 금액
        $total_withDraw = $total_withDraw_charge_amount - $total_withDraw_stock_used_amount;    // 현재 사용 가능 전체 예치금

        // 현재 투자 중 금액
        $ingStockTotalAmount = $this->ingStockTotalAmount($user_id);

        // 전세금 전체 금액
        $total_jeonse_amount = \App\Models\UserEstate::jeonse_total_assets($user_id);

        // 전체 투자 수익금액
        $total_dividend_amount = \App\Models\UserDividend::dividend_total_amount($user_id);

        // 예치금 + 전세금 + 현재 투자금액
        // $total = $total_withDraw + $ingStockTotalAmount;
        // $total = $total_withDraw + $total_jeonse_amount + $ingStockTotalAmount;
        $total = $total_withDraw + $total_jeonse_amount;


        // 투자 중 상품
        $data['stock_ing_contest_count'] = $this->ingStockContestCount($user_id);

        // 현재 시점 투자수익
        $data['now_dividend_amount'] = (int)\App\Models\UserDividend::dividend_ing_amount($user_id);

        // 누적 전체 투자금액
        $data['sum_total_stock_amount'] = (int)\App\Models\UserStock::totalStockAmount($user_id);

        // 등록된 전세 개수
        $data['register_jeonse_count'] = \App\Models\UserEstate::userRegisterLessorCount($user_id);

        // 내 전체 보유자산
        $data['total_amount'] = (int)$total;

        // 현재 사용 가능한 전체 예치금
        $data['can_use_withDraw_amount'] = (int)$total_withDraw;

        // 누적 수익금
        $data['sum_total_dividend_amount'] = (int)$total_dividend_amount;

        // 투자 가능한 예치금
        $data['can_stock_amount'] = (int)$total_withDraw;

        // 투자 중 금액
        $data['now_stocked_amount'] = $ingStockTotalAmount;

        return $data;

    }


    // 포트폴리오 상단 섹션 데이터
    protected function portfolio($user_id) {

        $data = [];

        // 현재 투자중 상품 건
        $data['now_stock_ing_count'] = $this->ingStockContestCount($user_id);

        // 현재 투자중 전체 금액
        $data['now_stock_ing_amount'] = $this->ingStockTotalAmount($user_id);

        // 누적 투자 금액
        $data['total_sum_stock_amount'] = (int)\App\Models\UserStock::totalStockAmount($user_id);

        // 누적 투자 수익 금액
        $data['total_sum_dividend_amount'] = (int)\App\Models\UserDividend::dividend_total_amount($user_id);

        return $data;
    }


    // 수익 관리 상단 섹션 데이터
    protected function dividendSectionList($user_id) {

        $data = [];

        $data['total_sum_dividend_amount'] = \App\Models\UserDividend::dividend_total_amount($user_id);
        $data['total_sum_dividend_count'] = \App\Models\UserDividend::dividendCount($user_id);
        $data['total_sum_stock_amount'] = $this->ingStockTotalAmount($user_id);

        $data['can_use_deposit_amount'] = (int)$this->chargeTotalWithDraw($user_id) - (int)$this->usedTotalWithDraw($user_id);

        $data['widthList'] = \App\Models\UserRequestWithDrawal::getUserWithDrawSumAmount($user_id);

        return $data;
    }


    // 수익 관리 리스트
    protected function dividendList($user_id) {
        return $this->leftJoin('user_dividend', 'user_assets.ref_dividend_id', '=', 'user_dividend.id')
            ->leftJoin('contest', 'contest.id', '=', 'user_dividend.ref_contest_id')
            ->select(
                'user_assets.id',
                'user_assets.type_amount',
                'contest.title',
                'user_assets.type',
                'user_assets.amount',
                'user_assets.last_amount',
                'user_assets.created_at',
            )->where('user_assets.status', 0)->where('user_assets.ref_user_id', $user_id)
            ->orderByDesc('user_assets.id')
            ->get();
    }


    // 수익관리 검색 결과
    protected function dividendListSearchResult($user_id, $start_date, $end_date, $type) {
        $res = $this->leftJoin('user_dividend', 'user_assets.ref_dividend_id', '=', 'user_dividend.id')
            ->leftJoin('contest', 'contest.id', '=', 'user_dividend.ref_contest_id')
            ->select(
                'user_assets.id',
                'user_assets.type_amount',
                'contest.title',
                'user_assets.type',
                'user_assets.amount',
                'user_assets.last_amount',
                'user_assets.created_at',
            )->where('user_assets.status', 0)->where('user_assets.ref_user_id', $user_id)
            ->whereBetween('user_assets.created_at', [$start_date, $end_date]);

        if(!empty($start_date) && !empty($end_date) && !empty($type)) {
            $res->where('user_assets.type', $type);
        }

        return $res->orderByDesc('user_assets.id')->get();
    }


    // 출금 요청
    protected function setWithDrawAmount($user_id, $amount) {

        $lastResultGetLastAmount = $this->where('status', 0)->where('ref_user_id', $user_id)->orderByDesc('id')->first();

        $lastAmount = $lastResultGetLastAmount->last_amount;

        return $this->insertGetId([
            'ref_user_id' => $user_id,
            'type_amount' => 0,
            'type' => 2,
            'total_amount' => $lastAmount,
            'amount' => $amount,
            'last_amount' => ($lastAmount - $amount)
        ]);
    }


    // 현재 사용 가능한 금액 [ 투자, 출금 ]
    protected function getCanUseMyAmount($user_id) {
        return (int)$this->chargeTotalWithDraw($user_id) - (int)$this->usedTotalWithDraw($user_id);
    }


    // 투자한 금액이 현재 사용 가능한 금액을 초과하는지 체크
    protected function stockOverCheck($user_id, $stockAmount) {

        $now_have_money = $this->getCanUseMyAmount($user_id);

        $invalidCheck = false;
        if($now_have_money >= $stockAmount) {
            $invalidCheck = true;
        }

        return $invalidCheck;
    }


    // 투자 시, 변동되는 유저의 자산 업데이트
    protected function minusStockAssests($user_id, $type, $stockAmount, $admin_id = null) {

        $res = $this->select(
            'total_amount',
            'last_amount',
        )->where('status', 0)->where('ref_user_id', $user_id)->orderByDesc('id')->first();

        $last_amount = 0;
        if(!empty($res->last_amount)) {
            $last_amount = (int)$res->last_amount;
        }
        $amount = (int)$stockAmount;

        $update_last_amount = 0;

        if($type == 1) {
            $update_last_amount = $last_amount;
        } else {
            $update_last_amount = ($last_amount - $amount);
        }

        $this->insertGetId([
            'ref_user_id' => $user_id,
            'ref_admin_id' => $admin_id,
            'type_amount' => $type,
            'type' => 1,
            'total_amount' => $last_amount,
            'amount' => $amount,
            'last_amount' => $update_last_amount,
        ]);

    }

    /* --------------------------------------------------------------------------------------------------- */
    /* Private */
    /* --------------------------------------------------------------------------------------------------- */
    // 사용자 충전된 전체 예치금 총 금액
    private function chargeTotalWithDraw($user_id) {
        return $this->where('status', 0)->where('type_amount', 0)->whereIn('type', [0, 3])->where('ref_user_id', $user_id)->sum('amount');
    }

    // 사용자 사용한 예치금 총 금액
    private function usedTotalWithDraw($user_id) {
        return $this->where('status', 0)->where('type_amount', 0)->where('type', '!=', 0)->where('type', '!=', 1)->where('type', '!=', 3)
                    ->where('ref_user_id', $user_id)
                    ->sum('amount');
    }

    // 사용자 현재 투자 총 금액
    private function ingStockTotalAmount($user_id) { return (int)\App\Models\UserStock::ingStockAmount($user_id); }

    // 사용자 현재 투자 중 공모 개수
    private function ingStockContestCount($user_id) { return \App\Models\UserStock::ingStockContestCount($user_id); }
    /* --------------------------------------------------------------------------------------------------- */

    // [..ing] 회원자산 관리 테이블 '일반 투자금' 컬럼
    protected function admin_getUserTotalHaveAmount() {

        return $this->join(
            'users', 'users.id', '=', 'user_assets.ref_user_id'
        )->select(

            'user_assets.ref_user_id as user_id',
            DB::raw(
                '(
                    select SUM(amount)
                    from user_assets
                    where status = 0 and type = 0 and type_amount = 0 and ref_user_id = users.id
                ) as chargeSumAmount'
            ),

            DB::raw(
                '(
                    select SUM(amount)
                    from user_assets
                    where status = 0 and type = 2 and type_amount = 0 and ref_user_id = users.id
                ) as withDrawSumAmount'
            ),
        )
            ->where('user_assets.status', 0)
            ->where('user_assets.type', 0)
            ->where('user_assets.type_amount', 0)
            ->groupBy('user_assets.ref_user_id')
            ->get();

    }


    //
    protected function admin_deposit_list($keyword=null) {
        $res = $this->join(
            'users', 'users.id', '=', 'user_assets.ref_user_id'
        )
            ->select(
                'user_assets.id',
                'users.name',
                DB::raw(
                    '(
                        select name
                        from users
                        where id = user_assets.ref_user_id
                    ) as admin'
                ),
                'user_assets.type_amount',
                'user_assets.amount',
                'user_assets.created_at'
        )
            ->where('user_assets.status', 0)
            ->where('user_assets.type', 0);

        if(!empty($keyword)) {
            $res->where('users.name', 'like', '%'.$keyword.'%');
        }

        $res->whereNull('removed_at');

        return $res->get();
    }


    //
    protected function admin_dividend_register($dividend_id, $amount, $user_id, $type) {

        $res = $this->where('ref_user_id', $user_id)->latest()->first();

        $this->insert([
            'ref_user_id'       => $user_id,
            'ref_dividend_id'   => $dividend_id,
            'type_amount'       => 0,
            'type'              => 3,
            'total_amount'      => (double)$res['last_amount'],
            'amount'            => (double)$amount,
            'last_amount'       => ((double)$res['last_amount'] + (double)$amount),
        ]);

    }


    //
    protected function admin_update_user_cancle_assets($id, $admin_id) {

        $code = [];

        $targetRes = $this->where('id', $id)->first();

        $nowRes = $this->where('ref_user_id', $targetRes['ref_user_id'])->latest()->first();

        // 입금 요청된 건을 출금 처리 건으로 변경한다.
        // * 이 때, 사용자의 last_amount 를 확인하여 (-)가 아닌 경우 입금 건을 출금 건으로 변경한다.

        $target_lastAmount = (int)$targetRes['last_amount'];

        // 입금 처리된 PK 기준 변동될 금액 데이터
        $target_amount = (int)$targetRes['amount'];

        // 회원의 자산 이력 마지막 Raw 중 변경된 자산 금액
        $now_lastAmount = (int)$nowRes['last_amount'];

        // 회원의 자산 이력 마지막 Raw 중 변경될 금액
        $now_amount = (int)$nowRes['last_amount'];

        if(($now_amount-$target_amount) >= 0) {

            $type = $targetRes['type_amount'];

            $this->insert([
                'ref_user_id' => $targetRes['ref_user_id'],
                'ref_admin_id' => $admin_id,
                'type_amount' => $type,
                'type' => 2,
                'total_amount' => $now_lastAmount,
                'amount' => $target_amount,
                'last_amount' => ((int)$now_lastAmount - (int)$target_amount),
            ]);

            $code['key'] = 1;

            $this->where('id', $id)->update([
               'removed_at' => now(),
            ]);

        } else {

            $code['key'] = 2;
            $code['amount'] = $target_amount;

        }

        return $code;
    }


    //
    protected function admin_delete_content($id) {
        return $this->where('id', $id)->update([
            'removed_at' => now()
        ]);
    }

    /* --------------------------------------------------------------------------------------------------- */

    // [ ... ing ] 의견 추가 검토 필요
    protected function admin_update_user_assets($user_id, $type, $amount, $estate_amount, $admin_id) {

        // 투자금 업데이트 시, 사용자 자산 목록에 업데이트할 내용이 추가된다.
        // 더불어, 입금 내역 관리 리스트에서도 해당 내용이 쌓인다.

        $res = $this->where('ref_user_id', $user_id)->latest()->first();

        // total_amount 현재 자산
        // amount 변동 금액
        // last_amount 변동 금액으로 인해 현재 자산에서 변동된 값
        if((int)$amount > 0) {
            $this->insert([
                'ref_user_id' => $user_id,
                'ref_admin_id' => $admin_id,
                'type_amount' => 0,
                'type' => 0,
                'total_amount' => $res['last_amount'] ?? 0,
                'amount' => $amount,
                'last_amount' => (int)$amount + ($res['last_amount'] ?? 0),
            ]);
        }

        $res = null;
        $res = $this->where('ref_user_id', $user_id)->latest()->first();

        if((int)$estate_amount > 0) {
            $this->insert([
                'ref_user_id' => $user_id,
                'ref_admin_id' => $admin_id,
                'type_amount' => 1,
                'type' => 0,
                'total_amount' => $res['last_amount'],
                'amount' => $estate_amount,
                'last_amount' => (int)$amount + (int)$res['last_amount'],
            ]);
        }


    }



    /* --------------------------------------------------------------------------------------------------- */

    //
    protected function serializeDate(\DateTimeInterface $date): string { return $date->format('Y-m-d H:i:s'); }
}
