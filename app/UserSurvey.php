<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class UserSurvey extends Model
{

    protected $table = "user_survey";

    protected $fillable = [
        "id",
        "user_id",
        "survey_id",
    ];

    public $rules = [
        "user_id" => "required",
        "survey_id" => "required",
    ];

}
