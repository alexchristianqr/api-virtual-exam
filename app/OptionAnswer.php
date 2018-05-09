<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class OptionAnswer extends Model {

    protected $table = 'option_answer';

    protected $fillable = [
        'question_id',
        'name',
        'status',
    ];

    protected $dates = [];

    function returnRules($request)
    {
        $rules = [];
        switch ($request->method()){
            case 'POST':
                $rules = [
                    'question_id' => 'required',
                    'name' => 'required',
                ];
                break;
            case 'PUT':
                $rules = [
                    'question_id' => 'required',
                    'name' => 'required',
                    'status' => 'required',
                ];
                break;
        }
        return $rules;
    }

    // Relationships

}
