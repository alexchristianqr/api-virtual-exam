<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class UserSurveyTheme extends Model
{

  protected $table = 'user_survey_theme';
  protected $fillable = [
    'user_survey_id',
    'theme_id',
    'option_answer_ids',
    'date_start',
    'date_expired',
    'time_start',
    'time_expired',
    'score',
    'description',
    'status',
    'status_table',
  ];

}
