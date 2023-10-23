<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserJoinSurvey extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'user_join_survey';

    public function __construct() { return $this; }

    /* ---------------------------------------------------------- */

    //
    protected function store($user_id, $surveyArray) {

        foreach ($surveyArray as $surveyIndex) {
            $this->insert([
                'ref_user_id' => $user_id,
                'ref_join_survey_id' => $surveyIndex,
            ]);
        }
    }

}
