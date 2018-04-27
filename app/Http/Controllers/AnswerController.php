<?php

namespace App\Http\Controllers;

use App\Answer;
use Illuminate\Http\Request;

class AnswerController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    function all(Request $request)
    {
        $query = new Answer();
        if ($request->has('user_survey_theme_id')) {
            $query = $query->select([
                'theme.id AS theme_id',
                'theme.name AS theme_name',
                'theme.updated_at AS theme_updated_at',
                'theme.status AS theme_status',
                'user_survey_theme.id AS user_survey_theme_id',
                'user_survey_theme.status AS user_survey_theme_status',
            ])
                ->join('user_survey_theme', 'user_survey_theme.theme_id', 'theme.id')
                ->leftJoin('user_survey', 'user_survey.id', 'user_survey_theme.user_survey_id')
                ->where('theme.status', 'A')
                ->where('user_survey.id', $request->user_survey_theme_id)
                ->orderBy('theme.id');
        } else {
            $query = $query->select(['theme.*']);
        }
        return $query->get()->toArray();
    }

}
