<?php
/**
 * Created by PhpStorm.
 * User: aquispe
 * Date: 6/11/2018
 * Time: 4:19 PM
 */

namespace App\Http\Services;


use App\UserSurveyTheme;

class UserSurveyThemeService
{

  function getUserSurveyTheme()
  {
    return UserSurveyTheme::where('status', 'P')->get();
  }

  function updateStatus($id, $status)
  {
    return UserSurveyTheme::where('id', $id)->update(['status' => $status]);
  }

}