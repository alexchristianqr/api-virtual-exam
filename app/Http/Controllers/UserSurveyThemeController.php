<?php

namespace App\Http\Controllers;

use App\UserSurveyTheme;

class UserSurveyThemeController extends Controller
{

    function create($request)
    {
        $user_survey_theme = new UserSurveyTheme();
        $this->validate($request, $user_survey_theme->rules);
        return $user_survey_theme->create($request->all());
    }

    function update($request)
    {
        $user_survey_theme = new UserSurveyTheme();
        $this->validate($request, $user_survey_theme->rules);
        return $user_survey_theme
            ->where("user_survey.id", $request->theme_id)
            ->update(["user_survey_theme.status" => $request->status]);
    }

}

