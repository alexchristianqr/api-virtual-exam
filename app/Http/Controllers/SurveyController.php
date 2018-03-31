<?php

namespace App\Http\Controllers;

use App\Survey;
use Illuminate\Http\Request;

class SurveyController extends Controller
{
    function allByUserSurvey(Request $request)
    {
        return (new Survey())
            ->select([
                "survey.*",
                "user_survey.id AS user_survey_id",
            ])
            ->join("user_survey","user_survey.survey_id","survey.id")
            ->where("user_survey.user_id",$request->user_id)
            ->get()
            ->toArray();
    }

    function all(Request $request)
    {
        return (new Survey())
            ->select()
            ->get()
            ->toArray();
    }
}
