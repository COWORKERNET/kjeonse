<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLessee extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'user_lessee';


    // 임대인 등록 리스트
    protected function list($user_id) {
        return $this->select('ref_user_lessor_id')->where('status', 0)->where('ref_user_id', $user_id)->get();
    }

    //
    protected function serializeDate(\DateTimeInterface $date): string { return $date->format('Y-m-d H:i:s'); }
}
