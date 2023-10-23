<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserEstateQuestionCategory extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'user_estate_question_category';


    // 전세 현황 관리 시설 관리문의 카테고리 리스트
    protected function list() {
        return $this->select(
            'id',
            'title',
        )->where('status', 0)->get();
    }


    //
    protected function admin_get_detail_category() {
        return $this->select(
            'id',
            'title',
        )->where('status', 0)->get();
    }

    //
    protected function serializeDate(\DateTimeInterface $date): string { return $date->format('Y-m-d H:i:s'); }
}
