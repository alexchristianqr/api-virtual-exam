<?php

namespace App\Http\Controllers;

use App\Http\Services\SurveyService;
use Exception;
use Illuminate\Http\Request;

class UserSurveyController extends Controller
{

  /**
   * Crear en la tabla "user_survey"
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  function createUserSurvey(Request $request)
  {
    try {
      $return = (new SurveyService())->createUserSurvey($request);
      return response()->json($return , 200);
    } catch (Exception $e) {
      return response()->json($e->getMessage(), 412);
    }
  }

}
