<?php

namespace App\Http\Controllers;

use App\UserSurvey;
use Illuminate\Http\Request;

class UserSurveyController extends Controller
{
    function create(Request $request)
    {
        $theme_survey = new UserSurvey();
        $this->validate($request, $theme_survey->rules);
        return $theme_survey->create($request->all());
    }
}
