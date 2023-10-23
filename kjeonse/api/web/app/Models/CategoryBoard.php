<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryBoard extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'category_board';

    //
    protected function list() {
        return $this->select('id', 'title')->where('status', 0)->get();
    }

    protected function serializeDate(\DateTimeInterface $date): string { return $date->format('Y-m-d H:i:s'); }
}
