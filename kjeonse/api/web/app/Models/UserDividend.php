<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserDividend extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'user_dividend';


    //
    protected function list($user_id) {
        return $this->select(
            'ref_contest_id as id',
            'time',
            'dividend_amount',
            'created_at',
        )
        ->where('status', 0)
        ->where('ref_user_id', $user_id)
        ->get();
    }


    // 전체 누적 수익금
    protected function dividend_total_amount($user_id) {
        return $this->where('status', 0)->where('ref_user_id', $user_id)->sum('dividend_amount');
    }

    // 현재 시점 수익금
    protected function dividend_ing_amount($user_id) {
        return $this->where('status', 0)->where('type', 0)->where('ref_user_id', $user_id)->sum('amount');
    }


    //
    protected function dividendCount($user_id) {
        return $this->where('status', 0)->where('ref_user_id', $user_id)->count();
    }


    //
    protected function admin_getUserDividendSumAmount() {
        return $this->join(
            'users', 'users.id', '=', 'user_dividend.ref_user_id'
        )
            ->select(
            'user_dividend.ref_user_id as user_id',
            DB::raw(
                '(
                    select SUM(amount)
                    from user_dividend
                    where status = 0 and ref_user_id = users.id
                ) as dividendSumAmount'
            ),
        )
            ->where('user_dividend.status', 0)
            ->groupBy('user_dividend.ref_user_id')
            ->get();
    }


    //
    protected function admin_list() {

        // 공모명, 투자자 전체 수, 공모수량, 누적 배당 회차, 배당 시작일, 배당 마감일

        return $this->leftJoin(
            'contest', 'contest.id', '=', 'user_dividend.ref_contest_id'
        )->leftJoin(
            'user_stock', 'user_stock.id', '=', 'user_dividend.ref_contest_id'
        )
            ->select(
                'contest.id',
                'contest.title',
                'contest.cost',
                'contest.now_cost',
                'contest.expect_return_date as start_dividend_date',
                'contest.closed_dividend_at as end_dividend_date',
                DB::raw('(select count(ref_user_id) from user_dividend GROUP BY ref_user_id) as totalStockedPeople'),
                DB::raw('(select max(time) from user_dividend where user_dividend.ref_contest_id = contest.id) as sumDividendCount'),
                DB::raw('(select sum(stock_amount) from user_dividend where user_dividend.ref_contest_id = contest.id) as sumDividendStockAmount'),
            )
            ->where('user_dividend.status', 0)
            ->whereNull('contest.deleted_at')
            ->get();

    }


    // [c] *** 관리자 배당금 지급 ***
    protected function admin_register_deposit($contest_id, $amount , $option, $admin_id) {

        // 배당금 지급 알림 제목
        $contest = \App\Models\Contest::admin_get_contest_title($contest_id);

        // 배당금을 지급을 받을 유저 리스트
        $dividend_user_list = \App\Models\UserStock::admin_get_contest_stock_userList($contest_id);

        // 해당 공모전의 배당 마지막 회차
        $last_dividend_count = $this->where('ref_contest_id', $contest_id)->max('time');

        // 수익금 지급 신규 회차 번호
        $next_dividend_count = ($last_dividend_count+1);

        foreach ($dividend_user_list as $user) {

            $return_amount = 0;         // 지급 받을 수익금
            $return_amount_percent = 0; // 내 지급 배당 퍼센트
            $user_stocked_amount = 0;   // 투자 금액

            if($user['type'] == 0) {
                //
                $user_stocked_amount = (int)$user['userStockTotalSumAmount'];
            } else {
                //
                $user_stocked_amount = (int)$user['userStockTotalEstateSumAmount'];
            }

            $return_amount_percent = (double)((int)$amount/(int)$option['now_cost']);
            $return_amount = ($user_stocked_amount*$return_amount_percent);

            $res = $this->insertGetId([
                'type' => 0,
                'ref_admin_id' => $admin_id,
                'ref_contest_id' => $contest_id,
                'ref_user_id' => $user['id'],
                'time' => $next_dividend_count,
                'total_amount' => $amount,
                'amount' => $return_amount,
                'stock_amount' => $user_stocked_amount,
                'dividend_amount' => $return_amount
            ]);

            \App\Models\Notification::push_alaram_dividend($contest['title'], $user['id'], $res);
            \App\Models\UserAssets::admin_dividend_register($res, $return_amount, $user['id'], $user['type']);
        }

    }


    // AdminController::detail_dividend
    protected function admin_get_dividend_contest_count($contest_id) {
        return $this->where('ref_contest_id', $contest_id)->max('time');
    }

    //
    protected function serializeDate(\DateTimeInterface $date): string { return $date->format('Y-m-d H:i:s'); }
}
