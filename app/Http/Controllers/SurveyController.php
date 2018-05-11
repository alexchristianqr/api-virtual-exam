<?php

namespace App\Http\Controllers;

use App\Survey;
use App\UserSurvey;
use App\UserSurveyTheme;
use Illuminate\Http\Request;
use Exception;

class SurveyController extends Controller
{
    function allByUserSurvey(Request $request)
    {
        try {
            $Survey = Survey::select(['survey.*', 'user_survey.id AS user_survey_id'])
                ->join('user_survey', 'user_survey.survey_id', 'survey.id')
                ->where('user_survey.user_id', $request->user_id);

            return response()->json($Survey->get(), 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 412);
        }
    }

    function all(Request $request)
    {
        try {
            $Survey = Survey::select(['survey.*', 'user_survey.id AS user_survey_id'])
                ->join('user_survey', 'user_survey.survey_id', 'survey.id');

            if ($request->get('status') != "") $Survey = $Survey->where('survey.status', $request->status);
            return response()->json($Survey->get(), 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 412);
        }
    }

    function create(Request $request)
    {
        try {
            $Survey = new Survey();
            if (is_array($request->get('user_id'))) {
                foreach ($request->get('user_id') as $item) {
                    $Survey->fill($request->all())->save();
                    UserSurvey::create(['user_id' => $item, 'survey_id' => $Survey->id]);
                }
            } else {
                $Survey->fill($request->all())->save();
                UserSurvey::create(['user_id' => $request->user_id, 'survey_id' => $Survey->id]);
            }
            return response()->json($request->all(), 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 412);
        }

    }


}
