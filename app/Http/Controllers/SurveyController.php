<?php

namespace App\Http\Controllers;

use App\Survey;
use App\UserSurvey;
use Illuminate\Http\Request;
use Exception;

class SurveyController extends Controller
{
    function allByUserSurvey(Request $request)
    {
        return (new Survey())
            ->select([
                'survey.*',
                'user_survey.id AS user_survey_id',
            ])
            ->join('user_survey', 'user_survey.survey_id', 'survey.id')
            ->where('user_survey.user_id', $request->user_id)
            ->get()
            ->toArray();
    }

    function all(Request $request)
    {
        return (new Survey())->get()->toArray();
    }

    function create(Request $request)
    {
        try {
            $Survey = new Survey();
            if (is_array($request->get('user_id'))) {
                foreach ($request->get('user_id') as $item) {
                    $Survey->fill($request->all());
                    $Survey->save();
                    UserSurvey::create(['user_id' => $item, 'survey_id' => $Survey->id]);
                }
            } else {
                $Survey->fill($request->all())->save();
                UserSurvey::create(['user_id' => $request->user_id, 'survey_id' => $Survey->id]);
            }
            return response()->json('created category!', 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 412);
        }

    }



}
