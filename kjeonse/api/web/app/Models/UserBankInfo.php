<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBankInfo extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'user_bank_info';


    //
    protected function store($user_id, $bank_id, $bank_account_number) {
        return $this->insertGetId([
            'ref_user_id' => $user_id,
            'ref_bank_id' => $bank_id,
            'bank_account_number' => $bank_account_number,
        ]);
    }

    protected function serializeDate(\DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }
}
