<?php

namespace App\Http\Controllers;

use App\UserSurveyTheme;
use Exception;

class UserSurveyThemeController extends Controller
{

    function create($request)
    {
        $user_survey_theme = new UserSurveyTheme();
        $this->validate($request, $user_survey_theme->rules);
        try {
            return $user_survey_theme->create($request->all());
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 412);
        }
    }

    function update($request)
    {
        $user_survey_theme = new UserSurveyTheme();
        $this->validate($request, $user_survey_theme->rules);
        try {
            return $user_survey_theme
                ->where('user_survey.id', $request->theme_id)
                ->update(['user_survey_theme.status' => $request->status]);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 412);
        }
    }

}

