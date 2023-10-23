<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'bank';


    //
    protected function list() {
        return $this->select('id', 'bank_name')->where('status', 0)->get();
    }


    protected function serializeDate(\DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }
}
