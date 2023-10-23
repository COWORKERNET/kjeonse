<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeavedUser extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'leaved_user';


    // User leaved reason 탈퇴 회원 사유 저장
    protected function insertLeavedUser($user, $reason) {
        foreach ($reason as $item) {
            $this->insert([
                'ref_user_id' => $user,
                'ref_leaved_reason_id' => $item,
            ]);
        }
    }

    //
    protected function admin_leaved_list() {
        return $this->join(
            'leaved_reason_list', 'leaved_reason_list.id', '=', 'leaved_user.ref_leaved_reason_id'
        )->select(
            'leaved_user.ref_user_id as user_id',
            'leaved_reason_list.title',
        )->where('leaved_user.status', 0)
            ->where('leaved_user.status', 0)->get();
    }

    //
    protected function admin_update_user_leaved($id) {
        $res = $this->where('ref_user_id', $id)->get();

        if(count($res) < 1) {
            $this->insert([
                'status' => 0,
                'ref_user_id' => $id,
                'ref_leaved_reason_id' => 7,
            ]);
        }
    }

    // 관리자 탈퇴 회원 리스트에서 삭제
    protected function admin_delete_leaved_list($id) {
        return $this->where('ref_user_id', $id)->update([
            'status' => 1,
        ]);
    }

    /* ----------------------------------------------------------------------------------------------------- */
    protected function serializeDate(\DateTimeInterface $date): string { return $date->format('Y-m-d H:i:s'); }
}
