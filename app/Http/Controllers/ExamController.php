<?php

namespace App\Http\Controllers;

use App\Question;
use App\UserSurveyTheme;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;

class ExamController extends Controller
{

    private function allQuestionByTheme($theme_id, $colums = ['question.*'])
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

    function loadExam(Request $request)
    {
        try {
            $data = $this->allQuestionByTheme($request->theme_id, ['question.id', 'question.theme_id', 'question.name as question_name', 'question.image as question_image', 'theme.name as theme_name']);
            //Ciclo para recorrer los datos extraidos de la Base de Datos.
            foreach ($data as $k => $v) {
                $data[$k]['options_answers'] = (new OptionAnswerController)->allOptionAnswerByQuestion($v['id']);
            }
            //Alear las posiciones del arreglo asociativo.
            shuffle($data);
            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 412);
        }
    }

    function updateExam(Request $request)
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
                throw new Exception('Error, no se ha recibido el arreglo de respuestas.');
            }
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 412);
        }
    }

    function loadExamSolution(Request $request)
    {
        $dataUserSurveyTheme = UserSurveyTheme::where('id', $request->user_survey_theme_id)->first();
        $dataQuestions = Question::where('theme_id', $dataUserSurveyTheme->theme_id)->get();

        $dataExamSolution = [];
//        foreach ($dataQuestions as $dataQuestion) {
//            $dataExamSolution=
//        }

    }

    private function checkExam($request, $option_answer_ids)
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
        $correctas = 0;
        $incorrectas = 0;
        foreach ($dataSolution as $value) {
            if ($value) {
                $correctas = $correctas + 1;//sumar respuestas correctas
            } else {
                $incorrectas = $incorrectas + 1;//sumar respuestas incorrectas
            }
        }
        //Ejemplo: maxima nota 20
        $puntaje_total = DB::table('settings')->where('name', 'maximun_score')->first();
        //Ejemplo: 20 puntuacion_total / 50 preguntas = 0.4 puntuacion_por_pregunta
        $puntaje_por_pregunta = ((int)$puntaje_total->value / $count_dataQuestion);
        //Obtener puntaje de examen
        $puntaje = ($correctas * $puntaje_por_pregunta);
        $this->updateUserSurveyTheme($request, $puntaje, $option_answer_ids);
    }

    private function updateUserSurveyTheme($request, $puntaje, $option_answer_ids)
    {
        UserSurveyTheme::where('id', $request->user_survey_theme_id)->update(['option_answer_ids' => json_encode($option_answer_ids), 'score' => $puntaje, 'status' => 'DD']);
    }

}