<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserStock extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'user_stock';

    protected $casts = [
      'amount' => 'integer', 'totalSumStockAmount' => 'integer',
    ];


    /* -------------------------------------------------------------------------------------------------- */
    // User 투자 리스트
    protected function list($user_id) {

        return $this->join('contest', 'contest.id', '=', 'user_stock.ref_contest_id')
            ->leftJoin('user_dividend', 'user_dividend.ref_contest_id', '=', 'contest.id')
            ->select(
                'contest.title',
                'user_stock.created_at as involve_date',

                DB::raw('(
                    select SUM(user_dividend.dividend_amount)
                    from user_dividend
                    where ref_contest_id = contest.id
                    and ref_user_id = ' . $user_id .
                ')
                as totalSumDividendAmount'),

                DB::raw(
                    '(
                    select SUM(user_stock.amount)
                    from user_stock
                    where ref_contest_id = contest.id
                    and ref_user_id = ' . $user_id .
                ')
                as totalSumStockAmount'),
            )
            ->where('user_stock.ref_user_id', $user_id)
            ->groupBy('user_stock.ref_user_id')
            ->limit(2)
            ->get();
    }


    // 누적 투자 금액
    protected function totalStockAmount($user_id) { return $this->where('status', 0)->where('type', '!=', 1)->where('ref_user_id', $user_id)->sum('amount'); }


    // 종료된 투자를 제외한 현재 투자 중 총 금액
    protected function ingStockAmount($user_id) { return $this->where('status', 0)->where('type', '!=', 2)->where('ref_user_id', $user_id)->sum('amount'); }


    // 현재 투자 중 공모전 개수
    protected function ingStockContestCount($user_id) { return $this->where('status', 0)->where('type', '!=', 2)->where('ref_user_id', $user_id)->count(); }


    // 현재 투자 공모 정보 및 거래 내역 리스트
    protected function userStockList($user_id) {

        // 투자 중 공모전 대표 이미지
        // 투자 중 공모전 제목
        // 공모전에 투자한 총 금액 ( 토큰 )
        // 투자유형
        // 참여일
        // 누적수익
        // 수익 리스트

        return $this->leftJoin('contest', 'user_stock.ref_contest_id', '=', 'contest.id')
                    ->leftJoin('contest_files', function($query) {
                        $query->on('user_stock.ref_contest_id', '=', 'contest_files.id')
                            ->where('contest_files.type', 0);
                    })
                    ->leftJoin('user_dividend', 'contest.id', '=', 'user_dividend.ref_contest_id')
                    ->select(

                        'contest.id',
                        'contest.title',
                        'contest_files.fileAddress',

                        DB::raw('(
                            select created_at from user_stock where ref_contest_id = contest.id
                            and ref_user_id = ' . $user_id . ' order By created_at asc limit 1
                        ) as stock_start_date'),

                        DB::raw(
                            '(
                                select SUM(user_stock.buy_token_count)
                                from user_stock
                                where ref_contest_id = contest.id
                                and ref_user_id = ' . $user_id .
                            ') as totalSumStockTokenCount'),

                        DB::raw(
                            '(
                                select SUM(user_stock.amount)
                                from user_stock
                                where ref_contest_id = contest.id
                                and ref_user_id = ' . $user_id .
                            ') as totalSumStockAmount'),

                        DB::raw(
                            '(
                                select SUM(user_dividend.dividend_amount)
                                from user_dividend
                                where ref_contest_id = contest.id
                                and ref_user_id = ' . $user_id .
                            ') as totalSumDividendAmount'),
                    )
                    ->where('user_stock.ref_user_id', $user_id)
                    ->distinct()
                    ->get();

    }


    // 투자 신청
    protected function stocked($user_id, $now_token_amount, $data, $admin_id = null) {

        $estate_id = !empty($data['estate_id']) ? $data['estate_id'] : null;

        $contest_id = null;
        $buy_token_count = null;

        if(!empty($data['contest_id'])) {
            $contest_id = $data['contest_id'];
            $buy_token_count = $data['buy_token_count'];
        } else {
            $contest_id = $data['cid'];
            $buy_token_count = $data['token'];
        }

        return $this->insertGetId([
            'ref_admin_id' => $admin_id,
            'type' => $data['type'],
            'ref_user_lessor_id' => $estate_id,
            'amount' => $data['amount'],
            'ref_contest_id' => $contest_id,
            'ref_user_id' => $user_id,
            'token_amount' => $now_token_amount,
            'buy_token_count' => $buy_token_count,
        ]);
    }


    // 전세 자금을 통해 투자한 모든 금액 합산
    protected function getStockedEstateAmount($user_id) {
        return $this->where('status', 0)->where('type', 1)->where('ref_user_id', $user_id)->whereNotNull('ref_user_lessor_id')->sum('amount');
    }


    // 특정 전세 자금을 통해 투자한 금액 전체 금액
    protected function getStockedEstateTargetAmount($user_id, $estate_id) {
        return $this->where('status', 0)
            ->where('type', 1)
            ->where('ref_user_id', $user_id)
            ->where('ref_user_lessor_id', $estate_id)
            ->sum('amount');
    }


    //
    protected function stockTypeList($user_id) {
        return $this->select('ref_contest_id as id', 'type')->where('ref_user_id', $user_id)->orderBy('id', 'asc')->get();
    }


    //
    protected function getStockedBuyTokenCount($user_id, $contest_id) {

        return $this->where('status', 0)
                    ->where('ref_user_id', $user_id)
                    ->where('ref_contest_id', $contest_id)
                    ->sum('buy_token_count');
    }


    // 회원 자산 입/출금/투자 기록 중 전세 자금에서 투자된 현황 리스트 조회
    protected function getEstateStockedHistory($user_id, $contest_id) {
        return $this->select(
            'ref_user_lessor_id as estate_id',
            DB::raw('sum(amount) as totalSumStockAmount'),
        )
            ->where('status', 0)
            ->where('type', 1)
            //->where('ref_contest_id', $contest_id)
            ->where('ref_user_id', $user_id)
            ->whereNotNull('ref_user_lessor_id')
            ->groupBy('estate_id')
            ->get();
    }


    /* ---------------------------------------------------------------------- */
    // 사용자 일반 예치금 전체 합산 금액
    protected function admin_getUserAmount() {
        return $this->join(
            'users', 'users.id', '=', 'user_stock.ref_user_id'
        )->select(
            'user_stock.ref_user_id',
            DB::raw(
            '(
                    select SUM(amount)
                    from user_stock
                    where status = 0 and type = 0 and ref_user_id = users.id
                ) as totalSumAmount'
            )
        )
            ->where('user_stock.status', 0)
            ->where('user_stock.type', 0)
            ->groupBy('user_stock.ref_user_id')
            ->get();
    }


    // 사용자 전세 투자금 전체 합산 금액
    protected function admin_getUserEstateAmount() {
        return $this->join(
            'users', 'users.id', '=', 'user_stock.ref_user_id'
        )->select(
            'user_stock.ref_user_id as user_id',
            DB::raw(
                '(
                    select SUM(amount)
                    from user_stock
                    where status = 0 and type = 1 and ref_user_id = users.id
                ) as totalSumAmount'
            )
        )
            ->where('user_stock.status', 0)
            ->where('user_stock.type', 1)
            ->groupBy('user_stock.ref_user_id')
            ->get();
    }


    // 배당 관리 상세 - 투자자 목록 리스트
    protected function admin_contest_stock_people_list($id, $time=0, $keyword='') {

        // 투자자명, 전화번호, 공모수량, 일반 투자금, 전세투자금, 배당금, 누적배당금, 투자일자

        $res = $this->join(
            'users', 'users.id', '=', 'user_stock.ref_user_id'
        )->join(
            'contest', 'contest.id', '=', 'user_stock.ref_contest_id'
        )->leftJoin(
            'user_dividend', 'user_dividend.ref_user_id', '=', 'user_stock.ref_user_id'
        )
            ->select(
            'contest.id as contest_id',
            'users.id as user_id',
            'users.name',
            'users.phone',
            'user_dividend.amount as userDividendCountSelectAmount',
            DB::raw('(select SUM(amount) from user_stock where ref_contest_id = contest.id and ref_user_id = users.id) as totalSumStockTokenAmount'),
            DB::raw('(select SUM(amount) from user_stock where ref_contest_id = contest.id and ref_user_id = users.id and type = 0 ) as totalSumStockAmount'),
            DB::raw('(select SUM(amount) from user_stock where ref_contest_id = contest.id and ref_user_id = users.id and type = 1 ) as totalSumStockEstateAmount'),
            DB::raw('(select SUM(amount) from user_dividend where ref_contest_id = contest.id and ref_user_id = users.id) as totalSumDividendAmount'),
            'user_stock.created_at',
        )
            ->where('user_stock.ref_contest_id', $id);

        if($time > 0) {
            $res->where('user_dividend.time', $time);
        }

        if(!empty($keyword)) {
            $res->where('users.name', 'like', '%'.$keyword.'%');
        }

        return $res->groupBy('users.id')->get();
    }


    // UserDividend::admin_register_deposit 함수에서 호출
    protected function admin_get_contest_stock_userList($contest_id) {

        /* - 배당금 지급을 위해 투자자 목록 리스트 반환 -*/
        return $this->join(
            'users', 'users.id', '=', 'user_stock.ref_user_id'
        )->select(
            'users.id',
            'user_stock.ref_user_id as user_id',
            'user_stock.type',
            DB::raw('( select SUM(amount) from user_stock where ref_user_id = users.id and type = 0 and ref_contest_id = '. $contest_id .') as userStockTotalSumAmount'),
            DB::raw('( select SUM(amount) from user_stock where ref_user_id = users.id and type = 1 and ref_contest_id = '. $contest_id .') as userStockTotalEstateSumAmount'),
        )
            ->where('user_stock.status', 0)
            ->where('user_stock.type', '!=', 2)
            ->where('user_stock.ref_contest_id', $contest_id)
            ->groupBy('users.id', 'user_stock.type')
            ->get();

    }


    //
    protected function admin_delete_stock_user($user_id, $contest_id) {
        return $this->where('ref_user_id', $user_id)->where('ref_contest_id', $contest_id)->update([
            'type' => 2
        ]);
    }

    /* ---------------------------------------------------------------------- */

    //
    protected function serializeDate(\DateTimeInterface $date): string { return $date->format('Y-m-d H:i:s'); }
}
