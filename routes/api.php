<?php

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Unauthorized
Route::post('/login', 'Auth\LoginController@login');
Route::post('/register', 'Auth\LoginController@register');

Route::group(['middleware' => ['cors:api']], function () {

    //Survey
    Route::get('/all-survey', 'SurveyController@all');
    Route::get('/all-by-user-survey', 'SurveyController@allByUserSurvey');
    Route::post('/create-survey', 'SurveyController@create');
    Route::put('/update-user-survey-theme', 'SurveyController@update');
    //Theme
    Route::get('/all-theme', 'ThemeController@all');
    Route::post('/create-theme', 'ThemeController@create');
    Route::put('/update-theme', 'ThemeController@update');
    //Exam
    Route::get('/load-exam', 'ExamController@exam');
    Route::post('/create-exam', 'ExamController@create');
    Route::put('/update-exam', 'ExamController@update');
    //Question
    Route::get('/all-question', 'QuestionController@all');
    Route::post('/create-question', 'QuestionController@create');
    Route::put('/update-question/{question_id}', 'QuestionController@update');
    //Answer
    Route::get('/all-answer', 'AnswerController@all');
    //Option Answer
    Route::get('/all-option-answer', 'OptionAnswerController@all');
    Route::post('/create-option-answer', 'OptionAnswerController@create');
    Route::put('/update-option-answer', 'OptionAnswerController@update');
    //Logout
    Route::post('/logout', 'AuthController@logout');

});