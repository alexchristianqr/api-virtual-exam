<?php


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

Route::group(['middleware' => ['cors:api']], function ($route) {
    //Validate
    $route->post('/if-exist-user', 'Auth\LoginController@validateIfExist');
    //Project
    $route->get('/all-project', 'ProjectController@all');
    $route->put('/update-project/{user_id}', 'ProjectController@update');
    //Survey
    $route->get('/all-survey', 'SurveyController@all');
    $route->get('/all-by-user-survey', 'SurveyController@allByUserSurvey');
    $route->post('/create-survey', 'SurveyController@create');
    $route->put('/update-user-survey-theme', 'SurveyController@update');
    //Theme
    $route->get('/all-theme', 'ThemeController@all');
    $route->post('/create-theme', 'ThemeController@create');
    $route->put('/update-theme', 'ThemeController@update');
    //Exam
    $route->get('/load-exam', 'ExamController@exam');
    $route->post('/create-exam', 'ExamController@create');
    $route->put('/update-exam', 'ExamController@update');
    //Question
    $route->get('/all-question', 'QuestionController@all');
    $route->post('/create-question', 'QuestionController@create');
    $route->put('/update-question/{question_id}', 'QuestionController@update');
    //Answer
    $route->get('/all-answer', 'AnswerController@all');
    //Option Answer
    $route->get('/all-option-answer', 'OptionAnswerController@all');
    $route->post('/create-option-answer', 'OptionAnswerController@create');
    $route->put('/update-option-answer', 'OptionAnswerController@update');
    //Logout
//    $route->post('/logout', 'AuthController@logout');

});