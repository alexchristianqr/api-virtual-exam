<?php

namespace App\Http\Controllers;

use App\Question;
use App\UserSurveyTheme;
use Illuminate\Http\Request;
use Exception;

class ExamController extends Controller
{

    function allQuestionByTheme($theme_id, $colums = ['question.*'])
    {
        $data = (new Question())
            ->select($colums)
            ->join('theme', 'theme.id', 'question.theme_id')
            ->where('question.theme_id', $theme_id)
            ->where('question.status', 'A')
            ->get()
            ->toArray();
        return $data;
    }

    function exam(Request $request)
    {
        $data = $this->allQuestionByTheme($request->theme_id, ['question.id', 'question.theme_id', 'question.name as question_name', 'question.image as question_image', 'theme.name as theme_name']);
        //Ciclo para recorrer los datos extraidos de la Base de Datos.
        foreach ($data as $k => $v) {
            $data[$k]['options_answers'] = (new OptionAnswerController)->allOptionAnswerByQuestion($v['id']);
        }
        //Reordenar las posiciones del arreglo asociativo.
        shuffle($data);
        return $data;
    }

    function update(Request $request)
    {
        try {
            if (is_array($request->answer_by_question)) {
                $option_answer_ids = [];
                foreach ($request->answer_by_question as $k => $v) {
                    if (!empty($v)) {
                        array_push($option_answer_ids, ['question_id' => $v['question_id'], 'option_answer_ids' => $v['option_answer_id']]);
                    }
                }
//            $encode = json_encode($option_answer_ids);
//            $decode = json_decode($encode);
//            dd($decode);
                UserSurveyTheme::where('id', $request->user_survey_theme_id)->update(['option_answer_ids' => json_encode($option_answer_ids), 'status' => 'P']);
                $this->checkExam($request);
                return response()->json('updated!', 200);
            } else {
               throw new Exception('No es un array()');
            }
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 412);
        }
    }

    function solution($request)
    {

    }

    function checkExam($request)
    {
        $dataUserSurveyTheme = UserSurveyTheme::where('id', $request->user_survey_theme_id)->first();
        $dataQuestion = Question::where('question.theme_id',$dataUserSurveyTheme->theme_id)->get();
        $decode_option_answer_ids = json_decode($dataUserSurveyTheme->option_answer_ids);

        $dataSolution = [];

        foreach ($dataQuestion as $k => $v){
            foreach ($decode_option_answer_ids as $vv){
                dd($v->id,$vv->question_id);
                if($v->id == $vv->question_id){
                    array_push($dataSolution,$v->option_answer_id == $vv->option_answer_ids);
                }else{
                    array_push($dataSolution, false);
                }
            }
        }

        dd($dataSolution);
    }

}