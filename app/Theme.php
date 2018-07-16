<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
  protected $table = 'theme';
  protected $fillable = [
    'survey_id',
    'name',
    'duration',
    'limit_questions',
    'description',
    'status',
  ];
}