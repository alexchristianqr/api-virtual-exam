<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class UserSurveyTheme extends Model {

    protected $table = "user_survey_theme";

    protected $fillable = [
        "user_survey_id",
        "theme_id",
        "status",
    ];

    protected $rules = [
        'user_survey_id' => 'required',
        'theme_id' => 'required',
    ];

}
