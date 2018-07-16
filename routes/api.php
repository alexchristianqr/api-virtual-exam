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
//Route::post('/login', 'Auth\LoginController@login');

Route::group(['middleware' => ['cors:api']], function ($route) {

    //User
    $route->get('/get-users', 'UserController@getUsers');
    $route->get('/all-user', 'UserController@all');
    $route->post('/u-object-rest-apis-application', 'UserController@getConfig');

    //Roles
    $route->get('/all-role', 'RoleController@all');

    //Validate Exist User
    $route->post('/if-exist-user', 'Auth\LoginController@validateIfExist');

    //Project
    $route->get('/all-project', 'ProjectController@all');
    $route->put('/update-project/{user_id}', 'ProjectController@update');

    //Survey
    $route->get('/get-surveys', 'SurveyController@getSurveys');
    $route->get('/get-surveys-by-user-survey', 'SurveyController@getSurveysByUserSurvey');
    $route->post('/create-survey', 'SurveyController@createSurvey');

    // User-Survey
    $route->post('/create-user-survey', 'UserSurveyController@createUserSurvey');

    //User-Survey-Theme
    $route->post('/create-user-survey-theme', 'UserSurveyThemeController@create');
    $route->get('/get-user-history', 'UserSurveyThemeController@userHistory');

    //Theme
    $route->get('/get-themes-by-user-survey-theme', 'ThemeController@getThemesByUserSurveyThemeId');
    $route->get('/get-themes-by-survey', 'ThemeController@getThemesBySurveyId');
    $route->post('/create-theme', 'ThemeController@create');
    $route->put('/update-theme', 'ThemeController@update');

    //Exam
    $route->get('/load-exam', 'ExamController@loadExam');
    $route->get('/load-exam-solution', 'ExamController@loadExamSolution');
    $route->get('/verify-exam-solution', 'ExamController@verifyExamSolution');
    $route->post('/create-exam', 'ExamController@createExam');
    $route->put('/update-exam', 'ExamController@updateExam');

    //Question
    $route->get('/all-question', 'QuestionController@all');
    $route->post('/create-question', 'QuestionController@create');
    $route->put('/update-question/{question_id}', 'QuestionController@update');

    //OptionAnswer
    $route->get('/all-option-answer', 'OptionAnswerController@all');
    $route->post('/create-option-answer', 'OptionAnswerController@create');
    $route->put('/update-option-answer', 'OptionAnswerController@update');

});