<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class UserLessor extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'user_lessor';


    /* -------------------------------------------------------------------------------------------------- */
    // 매핑된 전세 현황 리스트
    protected function list($user_id) {

        $lessor = $this->select(
            'title',
            'address',
            'address_detail',
            'contractor_open_at',
            'contractor_closed_at',
            'contractor_total_amount',
            'contractor_payment',
            'contractor_interim',
            'contractor_balance',
        )->where('status', 0)->where('ref_user_id', $user_id)->get();

        if(count($lessor) < 1) {

            $lessee = \App\Models\UserLessee::list($user_id);

            if(!empty($lessee)) {
                return $this->select(
                    'title',
                    'address',
                    'address_detail',
                    'contractor_open_at',
                    'contractor_closed_at',
                    'contractor_total_amount',
                    'contractor_payment',
                    'contractor_interim',
                    'contractor_balance',
                )->whereIn('id', $lessee->toArray())->get();
            }

        }

        return $lessor;
    }


    // 등록한 전세 자금 총합산
    protected function jeonse_total_assets($user_id) {

        $res = $this->select('contractor_total_amount', 'contractor_payment', 'contractor_interim', 'contractor_balance')
                    ->where('status', 0)->where('admin_approve', 1)->where('ref_user_id', $user_id)
                    ->get();

        $total = 0;
        if(empty($res)) return $total;

        foreach ($res as $jeonse) {
            $total += ($jeonse->contractor_payment + $jeonse->contractor_interim + $jeonse->contractor_balance);
        }

        return $total;
    }


    // 사용자 등록한 전세 개수
    protected function userRegisterLessorCount($user_id) {
        return $this->where('status', 0)->where('admin_approve', 1)->where('ref_user_id', $user_id)->count();
    }


    //
    protected function serializeDate(\DateTimeInterface $date): string { return $date->format('Y-m-d H:i:s'); }
}
