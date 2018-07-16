<?php

namespace App\Http\Controllers;

use App\Http\Services\SurveyService;
use Illuminate\Http\Request;
use Exception;

class SurveyController extends Controller
{

  /**
   * Cosultar la tabla "survey" por la tabla relacionada "user_survey"
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  function getSurveysByUserSurvey(Request $request)
  {
    try {
      return response()->json((new SurveyService())->getSurveys_by_UserSurvey($request), 200);
    } catch (Exception $e) {
      return response()->json($e->getMessage(), 412);
    }
  }

  /**
   * Consultar la tabla "survey"
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  function getSurveys(Request $request)
  {
    try {
      return response()->json((new SurveyService())->getSurveys($request), 200);
    } catch (Exception $e) {
      return response()->json($e->getMessage(), 412);
    }
  }

  /**
   * Crear en la tabla "survey"
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  function createSurvey(Request $request)
  {
    try {
      $return = (new SurveyService())->createSurvey($request);
      return response()->json($return, 200);
    } catch (Exception $e) {
      return response()->json($e->getMessage(), 412);
    }
  }

}
