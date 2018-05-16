<?php

namespace App\Http\Controllers;

use App\UserSurvey;
use Illuminate\Http\Request;

class UserSurveyController extends Controller
{
    function create(Request $request)
    {
        return (new UserSurvey())->create($request->all());
    }
}
