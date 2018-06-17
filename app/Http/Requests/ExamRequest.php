<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExamRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [];

        switch ($this->method()) {
            case 'POST':
                $rules = [
                    'theme_id' => 'required',
                    'name' => 'required',
                    'level' => 'required',
                ];
                break;
            case 'PUT':
                $rules = [
                    'theme_id' => 'required',
                    'name' => 'required',
                    'level' => 'required',
                    'status' => 'required',
                ];
                break;
        }
        return $rules;
    }
}
