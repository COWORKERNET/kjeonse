<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemDividendTime extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'dividend_time';



    //
    protected function serializeDate(\DateTimeInterface $date): string { return $date->format('Y-m-d H:i:s'); }
}
