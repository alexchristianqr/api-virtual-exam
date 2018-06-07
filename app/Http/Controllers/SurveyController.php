<?php

namespace App\Http\Controllers;

use App\Survey;
use App\UserSurvey;
use Illuminate\Http\Request;
use Exception;

class SurveyController extends Controller
{
  use SurveyService;

  function getSurveysByUserSurvey(Request $request)
  {
    try {
      return response()->json($this->prepare($request)->get(), 200);
    } catch (Exception $e) {
      return response()->json($e->getMessage(), 412);
    }
  }

  function getSurveys(Request $request)
  {
    try {
      return response()->json($this->prepare($request)->get(), 200);
    } catch (Exception $e) {
      return response()->json($e->getMessage(), 412);
    }
  }

  function create(Request $request)
  {
    try {
      $Survey = new Survey();
      if (is_array($request->get('user_id'))) {
        $Survey->fill($request->all())->save();
        foreach ($request->get('user_id') as $item) {
          UserSurvey::create(['user_id' => $item['id'], 'survey_id' => $Survey->id]);
        }
        return response()->json($request->all(), 200);
      } else {
        throw new Exception('El parámetro $user_id no es un array.');
      }
    } catch (Exception $e) {
      return response()->json($e->getMessage(), 412);
    }
  }

}

trait SurveyService
{
  private function prepare($request)
  {
    if ($request->has('user_id')) {
      $data = Survey::select(['survey.*', 'user_survey.id AS user_survey_id'])
        ->join('user_survey', 'user_survey.survey_id', 'survey.id')
        ->where('user_survey.user_id', $request->user_id);
    } else {
      $data = Survey::select(['survey.*']);
    }
    if ($request->has('status')) {
      $data = $data->where('survey.status', $request->status);
    } else {
      $data = $data->where('survey.status', 'A');
    }
    return $data;
  }
}