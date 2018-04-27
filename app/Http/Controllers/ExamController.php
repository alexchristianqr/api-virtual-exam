<?php

namespace App\Http\Controllers;

use App\Question;
use App\UserSurveyTheme;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;

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
                if (count($request->answer_by_question)) {
                    foreach ($request->answer_by_question as $k => $v) {
                        if (!empty($v)) {
                            array_push($option_answer_ids, ['question_id' => $v['question_id'], 'option_answer_ids' => $v['option_answer_id']]);
                        } else {
                            array_push($option_answer_ids, array());
                        }
                    }
                }
                $this->checkExam($request, $option_answer_ids);
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

    function checkExam($request, $option_answer_ids)
    {
        $dataQuestion = Question::where('question.theme_id', $request->theme_id)->get();
        $count_dataQuestion = $dataQuestion->count();
        $dataSolution = [];
        foreach ($dataQuestion as $k => $v) {
            foreach ($option_answer_ids as $kk => $vv) {
                if ($v->id == $vv['question_id']) {
                    if (is_null($vv['option_answer_ids'])) {
                        $dataSolution[$v->id] = false;
                    } else {
                        $dataSolution[$v->id] = ($v->option_answer_id == $vv['option_answer_ids']);
                    }
                }
            }
        }
//        dd($dataSolution);
        $correctas = 0;
        $incorrectas = 0;
        foreach ($dataSolution as $value) {
            if ($value) {
                $correctas = $correctas + 1;
            } else {
                $incorrectas = $incorrectas + 1;
            }
        }
        // maxima nota 20
        // 4 preguntas cada una 5 puntos

        $maximun_score = DB::table('settings')->where('name','maximun_score')->first();
        $valor_maximun_score = (int)$maximun_score->value / $count_dataQuestion;

        $promedio = ($correctas * $valor_maximun_score);
        $this->getScore($request, $promedio, $option_answer_ids);
    }

    function getScore($request, $promedio, $option_answer_ids)
    {
        UserSurveyTheme::where('id', $request->user_survey_theme_id)->update(['option_answer_ids' => json_encode($option_answer_ids), 'score' => $promedio, 'status' => 'DD']);
    }

}