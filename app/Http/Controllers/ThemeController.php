<?php

namespace App\Http\Controllers;

use App\UserSurvey;
use Illuminate\Http\Request;
use App\Theme;
use Exception;
use Illuminate\Support\Facades\DB;

class ThemeController extends Controller
{
    function all(Request $request)
    {
        try {
            $Theme = new Theme();
            if ($request->has('user_survey_theme_id')) {
                $Theme = $Theme->select([
                    'theme.id AS theme_id',
                    'theme.name AS theme_name',
                    'theme.updated_at AS theme_updated_at',
                    'theme.duration AS theme_duration',
                    'theme.date_start AS theme_date_start',
                    'theme.date_expired AS theme_date_expired',
                    'theme.status AS theme_status',
                    'user_survey_theme.id AS user_survey_theme_id',
                    'user_survey_theme.score AS user_survey_theme_score',
                    'user_survey_theme.status AS user_survey_theme_status',
                ])
                    ->join('user_survey_theme', 'user_survey_theme.theme_id', 'theme.id')
                    ->leftJoin('user_survey', 'user_survey.id', 'user_survey_theme.user_survey_id')
                    ->where('theme.status', 'A')
                    ->where('user_survey.id', $request->user_survey_theme_id)
                    ->orderBy('theme.id');
            } else {
                $Theme = $Theme->select(['theme.*']);
            }
            return response()->json($Theme->get(), 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 412);
        }
    }

    function create(Request $request)
    {
        $Theme = new Theme();
        DB::beginTransaction();
        try {
            $Theme->fill($request->all())->save();
            $request->request->add(['theme_id' => $Theme->id]);
            $user_survey_ids = UserSurvey::where('user_survey.survey_id', $request->survey_id)->get(['user_survey.id']);
            foreach ($user_survey_ids as $k => $v) {
                $request->request->add(['user_survey_id' => $v->id]);
                (new UserSurveyThemeController())->create($request);
            }
            DB::commit();
            return response()->json($request->all(),200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json($e->getMessage(), 412);
        }
    }
}
