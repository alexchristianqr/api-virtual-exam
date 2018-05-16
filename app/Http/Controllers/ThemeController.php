<?php

namespace App\Http\Controllers;

use App\UserSurvey;
use App\UserSurveyTheme;
use Illuminate\Http\Request;
use App\Theme;
use Exception;
use Illuminate\Support\Facades\DB;

class ThemeController extends Controller
{
  use ServiceTheme;

  function getThemesByUserSurveyThemeId(Request $request)
  {
    try {
      return response()->json($this->prepare($request)->get(), 200);
    } catch (Exception $e) {
      return response()->json($e->getMessage(), 412);
    }
  }

  function getThemesBySurveyId(Request $request)
  {
    try {
      return response()->json($this->prepare($request)->get(), 200);
    } catch (Exception $e) {
      return response()->json($e->getMessage(), 412);
    }
  }

  function create(Request $request)
  {
    DB::beginTransaction();
    try {
      (new Theme())->fill($request->all())->save();
      DB::commit();
      return response()->json($request->all(), 200);
    } catch (Exception $e) {
      DB::rollBack();
      return response()->json($e->getMessage(), 412);
    }
  }

}

trait ServiceTheme
{
  private function prepare($request)
  {
    if ($request->has('user_survey_theme_id')) {

      $data = Theme::select([
        'theme.id AS theme_id',
        'theme.name AS theme_name',
        'theme.updated_at AS theme_updated_at',
        'theme.duration AS theme_duration',
        'theme.status AS theme_status',
        'user_survey_theme.id AS user_survey_theme_id',
        'user_survey_theme.date_start AS user_survey_theme_date_start',
        'user_survey_theme.date_expired AS user_survey_theme_date_expired',
        'user_survey_theme.time_start AS user_survey_theme_time_start',
        'user_survey_theme.time_expired AS user_survey_theme_time_expired',
        'user_survey_theme.score AS user_survey_theme_score',
        'user_survey_theme.status AS user_survey_theme_status',
      ])
        ->join('user_survey_theme', 'user_survey_theme.theme_id', 'theme.id')
        ->leftJoin('user_survey', 'user_survey.id', 'user_survey_theme.user_survey_id')
        ->where('theme.status', 'A')
        ->where('user_survey.id', $request->user_survey_id)
        ->orderBy('theme.id');

    } elseif ($request->has('survey_id')) {

      $data = Theme::where('theme.survey_id', $request->survey_id);

    } else {

      $data = Theme::select(['theme.*']);

    }

    return $data;
  }
}