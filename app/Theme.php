<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Theme extends Model {

    protected $table = 'theme';

    protected $fillable = [
        'name',
        'date_start',
        'date_expired',
        'time_start',
        'time_expired',
        'duration',
        'status',
    ];

    public $rules = [
        'name' => 'required',
        'user_survey_id' => 'required'
    ];

}
