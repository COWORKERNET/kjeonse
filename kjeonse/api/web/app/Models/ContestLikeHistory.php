<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContestLikeHistory extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'history_likes';



    // 좋아요 클릭 함수
    protected function clickTolike($user_id, $contest_id) {

        $res = $this->where('status', 0)->where('ref_contest_id', $contest_id)->where('ref_user_id', $user_id)->first();

        if (!empty($res)) {

            $this->where('status', 0)
                ->where('ref_contest_id', $contest_id)
                ->where('ref_user_id', $user_id)
                ->update(['status' => 1]);

        } else {

            $this->insert([
                'ref_contest_id' => $contest_id,
                'ref_user_id' => $user_id,
            ]);

        }

    }


    //
    protected function list($user_id) {

        $res = $this->select('ref_contest_id')->where('status', 0)->where('ref_user_id', $user_id)->get();

        return \App\Models\Contest::likeContestList($res->toArray());

    }


    //
    protected function userLikeContestCheck($user_id, $id) {
        return $this->select('status as likes')
                    ->where('ref_contest_id', $id)
                    ->where('ref_user_id', $user_id)
                    ->where('status', 0)
                    ->first();
    }


    //
    protected function serializeDate(\DateTimeInterface $date): string { return $date->format('Y-m-d H:i:s'); }
}
