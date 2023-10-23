<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/*
 * @defined = 출금 요청 기록
 *
 */
class UserRequestWithDrawal extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'user_request_withDrawal';


    //
    protected function store($user_id, $userBankInfo_id, $stock_amount) {

        return $this->insertGetId([
            'ref_user_id' => $user_id,
            'ref_user_bank_info_id' => $userBankInfo_id,
            'withDraw_amount' => $stock_amount,
        ]);

    }


    // User /Mypage/ProfitMg : 누적 출금액
    protected function getUserWithDrawSumAmount($user_id) {
        return $this->where('ref_user_id', $user_id)->whereNotNull('completed_at')->sum('withDraw_amount');
    }


    //
    protected function admin_list($keyword = null) {
        $res = $this->join('users', 'users.id', '=', 'user_request_withDrawal.ref_user_id')
            ->join('user_bank_info', 'user_bank_info.id', '=', 'user_request_withDrawal.ref_user_bank_info_id')
            ->join('bank', 'bank.id', '=', 'user_bank_info.ref_bank_id')
            ->select(
                'user_request_withDrawal.id',
                'users.name',
                'users.phone',
                'bank.bank_name',
                'user_bank_info.bank_account_number',
                'user_request_withDrawal.withDraw_amount',
                'user_request_withDrawal.completed_at',
                'user_request_withDrawal.created_at'
        )
            ->where('user_request_withDrawal.status', 0);

        if(!empty($keyword)) {
            $res->where('name', 'like', '%'.$keyword.'%');
        }

        $res->whereNull('removed_at');
        // $res->groupBy('user_bank_info.id');
        $res->whereNull('user_request_withDrawal.removed_at');
        $res->orderByDesc('user_request_withDrawal.created_at');


        return $res->get();
    }


    //
    protected function admin_delete_content($id) {
        return $this->where('id', $id)->update([
            'removed_at' => now()
        ]);
    }


    //
    protected function admin_update_content($id) {
        return $this->where('id', $id)->update([
            'completed_at' => now()
        ]);
    }


    //
    protected function serializeDate(\DateTimeInterface $date): string { return $date->format('Y-m-d H:i:s'); }
}
