<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'history_notification';

    public function __construct() { return $this; }


    //
    protected function list($user_id) {

        $this->where('status', 0)->where('ref_user_id', $user_id)->whereNull('read_at')->update([
            'read_at' => now()
        ]);

        $res = $this->select(
            'title',
            'created_at'
        )
        ->where('status', 0)
        ->where('ref_user_id', $user_id)
        ->orderByDesc('id')
        ->get();

        return $res;
    }


    //
    protected function nonReadAlaramList($user_id) {
        return $this->where('ref_user_id', $user_id)
                    ->whereNull('read_at')
                    ->count();
    }


    // 배당금 지급 시, 알림 발송
    protected function push_alaram_dividend($title, $user_id, $dividend_id) {

        $text = "'".$title."'"."의 배당금이 입금되었습니다.";

        $this->insert([
            'status' => 0,
            'ref_user_id' => $user_id,
            'ref_pk_type' => 0,
            'ref_pk' => $dividend_id,
            'title' => $text,
        ]);
    }

    //
    protected function serializeDate(\DateTimeInterface $date): string { return $date->format('Y-m-d H:i:s'); }
}
