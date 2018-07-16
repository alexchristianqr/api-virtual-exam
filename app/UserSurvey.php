<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class UserSurvey extends Model
{

  protected $table = 'user_survey';
  protected $fillable = [
    'user_id',
    'survey_id',
  ];

}
