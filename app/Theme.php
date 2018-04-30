<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Theme extends Model {

    protected $table = 'theme';

    protected $fillable = [
        'name',
        'duration',
        'status',
    ];

    public $rules = [
        'name' => 'required',
        'user_survey_id' => 'required'
    ];

}
