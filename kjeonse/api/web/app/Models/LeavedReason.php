<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeavedReason extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'leaved_reason_list';


    //
    protected function list() {
        return $this->where('status', 0)->select('id', 'title')->get();
    }

    /* ----------------------------------------------------------------------------------------------------- */
    protected function serializeDate(\DateTimeInterface $date): string { return $date->format('Y-m-d H:i:s'); }
}
