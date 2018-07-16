<?php
/**
 * Created by PhpStorm.
 * User: aquispe
 * Date: 7/13/2018
 * Time: 3:04 PM
 */

namespace App\Http\Services;

use App\Survey;
use App\User;
use App\UserSurvey;

class SurveyService
{

  //Funcion modelo
  private function prepare($request = null)
  {
    if ($request->has('user_id')) {
      //Consultar con join
      $data = Survey::select(['survey.*', 'user_survey.id AS user_survey_id'])
        ->join('user_survey', 'user_survey.survey_id', 'survey.id')
        ->where('user_survey.user_id', $request->user_id)
        ->where('user_survey.status', 'A');
    } else {
      //Consultar sin join
      $data = Survey::select(['survey.*']);
    }
    //Si filtra por estado(status)
    if ($request->has('status')) {
      if ($request->get('status') != '') {
        $data = $data->where('survey.status', $request->status);
      }
    } else {
      $data = $data->where('survey.status', 'A');
    }
    return $data;
  }

  //Consultar tabla "survey"
  function getSurveys($request)
  {
    return $this->prepare($request)->get();
  }

  //Crear en la tabla "survey"
  function createSurvey($request)
  {
    $return = [];
    $newSurvey = new Survey();
    $newSurvey->fill($request->all())->save();
    $dataUser = User::select('id')->get();
    if ($dataUser->count() > 0) {
      foreach ($dataUser as $v) {
        (new UserSurvey())->fill(['user_id' => $v['id'], 'survey_id' => $newSurvey->id])->save();
        $return[] = $v['id'];
      }
    }
    return $return;
  }

  //Crear en la tabla "user_survey"
  function createUserSurvey($request)
  {
    $return = [];
    if (is_array($request->user_id)) {
      foreach ($request->user_id as $item) {
        $dataUserSurvey = UserSurvey::select('user_survey.survey_id')->where('user_survey.user_id', $item['id'])->get()->toArray();
        $dataSurvey = Survey::whereNotIn('survey.id', $dataUserSurvey)->get();
        if ($dataSurvey->count() > 0) {
          foreach ($dataSurvey as $v) {
            (new UserSurvey())->fill(['user_id' => $item['id'], 'survey_id' => $v->id])->save();
            $return[] = $item['id'];
          }
        }
      }
    } else {
      $dataUserSurvey = UserSurvey::select('user_survey.survey_id')->where('user_survey.user_id', $request->user_id)->get()->toArray();
      $dataSurvey = Survey::whereNotIn('survey.id', $dataUserSurvey)->get();
      if ($dataSurvey->count() > 0) {
        foreach ($dataSurvey as $v) {
          (new UserSurvey())->fill(['user_id' => $request->user_id, 'survey_id' => $v->id])->save();
          $return[] = $request->user_id;
        }
      }
    }
    return $return;
  }

  //Consultar tablas "survey" y "user_survey"
  function getSurveys_by_UserSurvey($request)
  {
    return $this->prepare($request)->get();
  }

}