<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Contest extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'contest';


    // User List
    protected function list($type, $keyword='') {

        $res = $this->leftJoin('contest_files', function ($join) {
                    $join->on('contest.id', '=', 'contest_files.ref_contest_id')
                    ->where('contest_files.type', 0)
                    ->where('contest_files.status', 0);
                })
                ->select(
                    'contest.id',
                    'contest.contest_status',
                    'contest.title',
                    'contest.feature',
                    'contest.description',
                    'contest.cost',
                    'contest.now_cost',
                    'contest.address',
                    'contest.opened_at',
                    'contest.closed_at',
                    'contest.token_count',
                    'contest.token_amount',
                    'contest.buy_token_count',
                    'contest_files.fileAddress',
                )->where('contest.status', 0);

        if(!empty($type)) {
            $res->where('contest.contest_status', $type);
        }

        if(!empty($keyword)) {
            $res->where('contest.title', 'like', '%'.$keyword.'%');
        }

        $res->orderByDesc('contest.id');

        return $res->paginate(5);
    }


    // Login User Contest List
    protected function loginUserContestList($type, $keyword='', $user_id) {

        $res = $this->leftJoin('contest_files', function ($join) {
                    $join->on('contest.id', '=', 'contest_files.ref_contest_id')
                        ->where('contest_files.type', 0);
                });

        if (!empty($user_id)) {
            $res->leftJoin('history_likes', function ($join) use ($user_id) {
                $join->on('contest.id', '=', 'history_likes.ref_contest_id')
                    ->where('history_likes.status', 0)
                    ->where('history_likes.ref_user_id', $user_id);
            });
        }

        $res->select(
            'contest.id',
            'contest.contest_status',
            'contest.title',
            'contest.feature',
            'contest.description',
            'contest.cost',
            'contest.now_cost',
            'contest.address',
            'contest.opened_at',
            'contest.closed_at',
            'contest.token_count',
            'contest.token_amount',
            'contest.buy_token_count',
            'contest_files.fileAddress',
            'history_likes.status as like',
        )->where('contest.status', 0);

        if(!empty($type)) {
            $res->where('contest.contest_status', $type);
        }

        if(!empty($keyword)) {
            $res->where('contest.title', 'like', '%'.$keyword.'%');
        }

        $res->groupBy('contest.id');
        $res->orderByDesc('contest.id');

        return $res->paginate(5);

    }


    // User Detail
    protected function detail($idx, $user_id = null,) {

        return $this->leftJoin('history_likes', function ($query) use ($user_id) {
               $query->on('history_likes.ref_contest_id', '=', 'contest.id')
                   ->where('history_likes.status', 0)
                   ->where('history_likes.ref_user_id', $user_id);
        })
        ->select(
            'contest.contest_status',
            DB::raw('(CASE WHEN (history_likes.status = null) THEN 0 else 1 end) as isLike'),
            //'history_likes.status as isLike',
            'contest.title',
            'contest.feature',
            'contest.description',
            'contest.content',
            'contest.info_title',
            'contest.cost',
            'contest.now_cost',
            'contest.address',
            'contest.opened_at',
            'contest.closed_at',
            'contest.token_count',
            'contest.token_amount',
            'contest.buy_token_count',
            'contest.expect_return_amount',
            'contest.expect_return_date',
            'contest.allocation_type',
            'contest.viewCount',
        )
        ->where('contest.status', 0)
        ->where('contest.id', $idx)
        ->first();
    }


    // User Like Pick Contest List
    protected function likeContestList(array $likeList) {

        $res = $this->leftJoin('contest_files', function ($join) {
            $join->on('contest.id', '=', 'contest_files.ref_contest_id')
                ->where('contest_files.type', 0);
        })
        ->select(
            'contest.id',
            'contest.contest_status',
            'contest.title',
            'contest.feature',
            'contest.description',
            'contest.cost',
            'contest.now_cost',
            'contest.address',
            'contest.opened_at',
            'contest.closed_at',
            'contest.token_count',
            'contest.token_amount',
            'contest.buy_token_count',
            'contest_files.fileAddress'
        )->where('contest.status', 0)->whereIn('contest.id', $likeList);

        return $res->get();

    }


    //
    protected function getContestTokenAmount($contest_id) {
        return $this->select('token_amount')->where('id', $contest_id)->first();
    }


    // 투자 시점, 투자한 금액 및 토큰 개수가 현재 공모전 투자 금액을 초과하는지 확인
    protected function stockOverCheck($contest_id, $stockTokenCount, $stockAmount): bool
    {

        $res = $this->select('cost', 'now_cost', 'buy_token_count')->where('id', $contest_id)->first();

        $max_stock_amount       = (int)$res->cost; // 1,000,000,000
        $now_stock_total_amount = (int)$res->now_cost; // 0
        $user_can_max_buy_token = (int)$res->buy_token_count; // 10,000,000

        $invalidCheck = false;

        if((($now_stock_total_amount + $stockAmount) <= $max_stock_amount)
            &&  ($user_can_max_buy_token >= $stockTokenCount))
        {
            $invalidCheck = true;
        }

        return $invalidCheck;

    }


    // 유저의 공모전 투자로 인해, 현재 모인 공모 금액 변경 시 업데이트
    protected function userStockedUpdateContestCost($contest_id, $stockAmount) {

        $res = $this->select('now_cost')->where('id', $contest_id)->first();

        $this->where('id', $contest_id)->update([
            'now_cost' => ((int)$res->now_cost + (int)$stockAmount)
        ]);
    }


    /* --------------------------------------------------------------*/
    // 공모전 리스트
    protected function admin_list($keyword = '') {

        $res = $this->select(
            'id',
            'contest_status',
            'title',
            'cost',
            'now_cost',
            'opened_at',
            'closed_at',
            'real_stocked_at',
        )
            ->where('status', 0);


        if(!empty($keyword)) {
            $res->where('title', 'like', '%'.$keyword.'%');
        }

        return $res->get();
    }


    // 공모전 삭제
    protected function admin_delete_contest($id) {
        return $this->where('id', $id)->update([
            'status' => 1,
        ]);
    }


    // 배당관리 리스트에서 삭제
    protected function admin_delete_dividend_list($id) {
        return $this->where('id', $id)->update([
           'deleted_at' => now(),
        ]);
    }


    // 배당관리 리스트
    protected function admin_dividend_list($keyword='') {

        // 공모명, 투자자 전체 수, 공모수량, 누적 배당 회차, 배당 시작일, 배당 마감일

        $res = $this->leftJoin(
            'user_dividend', 'contest.id', '=', 'user_dividend.ref_contest_id'
        )->leftJoin(
            'user_stock', 'user_stock.ref_contest_id', '=', 'contest.id'
        )
            ->select(
                'contest.id',
                'contest.title',
                'contest.cost',
                'contest.now_cost',
                'contest.expect_return_date as start_dividend_date',
                'contest.closed_dividend_at as end_dividend_date',

                DB::raw('(select count(distinct(ref_user_id)) from user_stock where ref_contest_id = contest.id) as totalStockedPeople'),

                DB::raw('(select max(time) from user_dividend where user_dividend.ref_contest_id = contest.id) as sumDividendCount'),

                DB::raw('(select sum(stock_amount) from user_dividend where user_dividend.ref_contest_id = contest.id) as sumDividendStockAmount'),
            )
            ->whereNull('contest.deleted_at');

        if(!empty($keyword)) {
            $res->where('contest.title', 'like', '%'.$keyword.'%');
        }

        $res->groupBy('contest.id');

        return $res->get();

    }


    // 공모전 등록
    protected function create($data, $admin_id) {

        return $this->insertGetId([
            'contest_status'        => $data['status'],
            'ref_admin_id'          => $admin_id,
            'title'                 => $data['title'],
            'feature'               => $data['feature'],
            'description'           => $data['description'],
            'content'               => $data['content'],
            'info_title'            => $data['info_title'],
            'cost'                  => $data['cost'],
            'token_count'           => ((int)$data['cost'] / 1000),
            'post_code'             => $data['post_code'],
            'address'               => $data['address'],
            'address_detail'        => $data['address_detail'],
            'opened_at'             => $data['opened_at'],
            'closed_at'             => $data['closed_at'],
            'buy_token_count'       => ((int)$data['can_buy_stock'] / 1000),
            'real_stocked_at'       => $data['real_stocked_at'],
            'expect_return_amount'  => $data['expect_return_amount'],
            'expect_return_date'    => $data['expect_return_date'],
            'allocation_type'       => $data['allocation_type'],
            'closed_dividend_at'    => $data['closed_dividend_at'],
        ]);

     }


    // 공모전 업데이트
    protected function updateContent($data, $admin_id) {

        return $this->where('id', $data['id'])->update([
            'contest_status'        => $data['status'],
            'ref_admin_id'          => $admin_id,
            'title'                 => $data['title'],
            'feature'               => $data['feature'],
            'description'           => $data['description'],
            'content'               => $data['content'],
            'info_title'            => $data['info_title'],
            'cost'                  => $data['cost'],
            'token_count'           => ((int)$data['cost'] / 1000),
            'address'               => $data['address'],
            'opened_at'             => $data['opened_at'],
            'closed_at'             => $data['closed_at'],
            'buy_token_count'       => ((int)$data['can_buy_stock'] / 1000),
            'real_stocked_at'       => $data['real_stocked_at'],
            'expect_return_amount'  => $data['expect_return_amount'],
            'expect_return_date'    => $data['expect_return_date'],
            'allocation_type'       => $data['allocation_type'],
            'closed_dividend_at'    => $data['closed_dividend_at'],
        ]);

    }


    // 배당 관리 상세
    protected function admin_detail_dividend($id) {

        // 공모수량, 투자자 수, 배당 시작일, 배당 마감일
        // 누적 배당금(원), 누적배당 회차, 총 배당 회 차(배당 타입 (주, 월, 년) 계산)

        return $this->leftJoin(
            'user_dividend', 'contest.id', '=', 'user_dividend.ref_contest_id'
        )->leftJoin(
            'user_stock', 'user_stock.ref_contest_id', '=', 'contest.id'
        )
            ->select(
                'contest.id',
                'contest.title',
                'contest.allocation_type',
                'contest.cost',
                'contest.now_cost',
                'contest.expect_return_date as start_dividend_date',
                'contest.closed_dividend_at as end_dividend_date',

                DB::raw('(select count(distinct(ref_user_id)) from user_stock where ref_contest_id = contest.id) as totalStockedPeople'),

                DB::raw('(select max(time) from user_dividend where user_dividend.ref_contest_id = contest.id) as sumDividendCount'),

                DB::raw('(select sum(distinct(total_amount)) from user_dividend where user_dividend.ref_contest_id = contest.id) as sumDividendStockAmount'),

            )
            ->whereNull('contest.deleted_at')
            ->where('contest.id', $id)
            ->groupBy('contest.id')
            ->get();

    }


    // 공모 상세
    protected function admin_detail($contest_id) {
        return $this->select(
            'contest_status',
            'title',
            'feature',
            'description',
            'content',
            'info_title',
            'cost',
            'now_cost',
            'post_code',
            'address',
            'address_detail',
            'info_title',
            'cost',
            'now_cost',
            'opened_at',
            'closed_at',
            'token_count',
            'buy_token_count',
            'real_stocked_at',
            'expect_return_amount',
            'expect_return_date',
            'allocation_type',
            'closed_dividend_at',
            'contest_status',
            'contest_status',
        )
            ->where('status', 0)
            ->where('id', $contest_id)
            ->first();
    }


    //
    protected function admin_get_contest_option($id) {
        return $this->select(
            'cost',
            'now_cost',
            'expect_return_amount'
        )->where('status', 0)->where('id', $id)->first();
    }


    //
    protected function admin_get_contest_title($contest_id) {
        return $this->select('title')->where('id', $contest_id)->first();
    }

    //
    protected function serializeDate(\DateTimeInterface $date): string { return $date->format('Y-m-d H:i:s'); }
}
