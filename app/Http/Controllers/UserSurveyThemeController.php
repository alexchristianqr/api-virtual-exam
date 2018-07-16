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
      foreach ($request->user_id as $k => $v) {
        $user_survey_id = UserSurvey::where('user_survey.survey_id', $request->survey_id)->where('user_survey.user_id', $v['id'])->pluck('user_survey.id')[0];
        $request->request->add(['user_survey_id' => $user_survey_id]);
        (new UserSurveyTheme())->fill($request->all())->save();
      }
      DB::commit();
      return response()->json($request->all(), 200);
    } catch (Exception $e) {
      DB::rollBack();
      $this->setTableAutoInc((new UserSurvey())->getTable());
      $this->setTableAutoInc((new UserSurveyTheme())->getTable());
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

  function userHistory(Request $request)
  {
    try {
      $data = UserSurveyTheme::select([
        'users.name as name',
        'theme.duration',
        'theme.name AS theme_name',
        'user_survey_theme.date_start',
        'user_survey_theme.date_expired',
        'user_survey_theme.status',
        'users.id AS user_id',
        'user_survey.id AS user_survey_id',
        'user_survey_theme.score',
        'user_survey_theme.id AS user_survey_theme_id',
        'user_survey_theme.status AS user_survey_theme_status'
      ])
        ->join('theme', 'theme.id', 'user_survey_theme.theme_id')
        ->join('user_survey', 'user_survey.id', 'user_survey_theme.user_survey_id')
        ->leftJoin('users', 'users.id', 'user_survey.user_id')
        ->where('users.id', $request->user_id)
        ->orderBy('user_survey_theme.date_start','DESC')
        ->get();
      return response()->json($data, 200);
    } catch (Exception $e) {
      return response()->json($e->getMessage(), 412);
    }
  }

}