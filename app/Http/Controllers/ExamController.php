<?php

namespace App\Http\Controllers;

use App\Question;
use App\UserSurveyTheme;
use Illuminate\Http\Request;
use Exception;

class ExamController extends Controller
{

    function allQuestionByTheme($theme_id, $colums = ["question.*"])
    {
        $data = (new Question())
            ->select($colums)
            ->join("theme", "theme.id", "question.theme_id")
            ->where("question.theme_id", $theme_id)
            ->where("question.status", "A")
            ->get()
            ->toArray();
        return $data;
    }

    function exam(Request $request)
    {
        $data = $this->allQuestionByTheme($request->theme_id, ["question.id", "question.theme_id", "question.name as question_name", "question.image as question_image", "theme.name as theme_name"]);
        //Ciclo para recorrer los datos extraidos de la Base de Datos.
        foreach ($data as $k => $v) {
            $data[$k]["options_answers"] = (new OptionAnswerController)->allOptionAnswerByQuestion($v['id']);
        }
        //Reordenar las posiciones del arreglo asociativo.
        shuffle($data);
        return $data;
    }

    function update(Request $request)
    {
        try {
           return  $this->correctAnswers($request);
//            UserSurveyTheme::where("id", $request->user_survey_theme_id)->update(['status' => 'D']);
//            return response()->json('updated', 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 412);
        }
    }

    function correctAnswers($request)
    {
        if (is_array($request->answer_by_question)) {
            $rpta = '';
            foreach ($request->answer_by_question as $k => $v) {
                if(!empty($v)){
                    $rpta = Question::where('id',$v['question_id'])->where('option_answer_id',$v['option_answer_id'])->first();
                    var_dump($rpta);
                }else{
                    $rpta = 'vacio';
                }
            }
            return response()->json($rpta, 200);
//            return $rpta;
        } else {
            return response()->json('no es un array', 412);
        }
    }

}