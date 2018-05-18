<?php

namespace App\Http\Controllers;

use App\UserSurvey;
use App\UserSurveyTheme;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserSurveyThemeController extends Controller
{
  function create(Request $request)
  {
    DB::beginTransaction();
    try {
//      $user_survey_ids = UserSurvey::where('user_survey.survey_id', $request->survey_id)
//        ->where('user_survey.user_id', $request->user_id)
//        ->get(['user_survey.id']);
      foreach ($request->user_id as $k => $v) {
        $user_survey_id = UserSurvey::where('user_survey.survey_id', $request->survey_id)->where('user_survey.user_id', $v['id'])->pluck('user_survey.id')[0];
        $request->request->add(['user_survey_id' => $user_survey_id]);
        (new UserSurveyTheme())->fill($request->all())->save();
      }


//      $user_survey_ids = UserSurvey::where('user_survey.survey_id', $request->survey_id)
//        ->where('user_survey.user_id', $request->user_id)
//        ->get(['user_survey.id']);
//      foreach ($user_survey_ids as $k => $v) {
//        $request->request->add(['user_survey_id' => $v['id']]);
//        (new UserSurveyTheme())->fill($request->all())->save();
//      }
      DB::commit();
      return response()->json($request->all(), 200);
    } catch (Exception $e) {
      DB::rollBack();
      return response()->json($e->getMessage(), 412);
    }
  }

  function update($request)
  {
    try {
      return (new UserSurveyTheme())->where('user_survey.id', $request->theme_id)->update(['user_survey_theme.status' => $request->status]);
    } catch (Exception $e) {
      return response()->json($e->getMessage(), 412);
    }
  }

}

