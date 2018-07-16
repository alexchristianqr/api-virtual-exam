<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{

  protected $table = 'question';
  protected $fillable = [
    'theme_id',
    'name',
    'option_answer_id',
    'level',
    'image',
    'image_url',
    'status',
  ];

}
