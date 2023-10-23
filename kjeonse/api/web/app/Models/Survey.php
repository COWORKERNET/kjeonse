<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'user_join_survey';


    protected function list($type) {
        return $this->join(
            'survey', 'survey.id', '=', 'user_join_survey.ref_join_survey_id'
        )->join(
            'users', 'users.id', '=', 'user_join_survey.ref_user_id'
        )
            ->select(
                'users.id',
                'survey.title',
            )
            ->where('users.status', 0)
            ->where('user_join_survey.status', 0)
            ->where('survey.ref_category_survey_id', $type)
            ->get();
    }


    //
    protected function serializeDate(\DateTimeInterface $date): string { return $date->format('Y-m-d H:i:s'); }
}
