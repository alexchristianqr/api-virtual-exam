<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExamRequest;
use App\OptionAnswer;
use App\Question;
use App\Theme;
use App\UserSurveyTheme;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;

class ExamController extends Controller
{

    function saveImage($image, $path, $requestImage)
    {
        //Guardar Original
        $dateFormat = date("dmyhis");
        //Cambiar de tamaño
//        $image->resize(240, 200);
        $image->save($path . $dateFormat . '_' . '.jpg');
//        $setImage = $dateFormat . '_' . $requestImage->getClientOriginalName();
//        return $setImage;
    }

    function createExam(ExamRequest $request)
    {
        DB::beginTransaction();
        try {
            $Question = new Question();
            $request_all = $request->all();
            $image = Image::make($request->image);
            $pathCopyOrigin = public_path() . '/load_images/copy/';
            $this->saveImage($image, $pathCopyOrigin, $request->image);

            $Question->fill($request->all());
            $Question->image = 'logo.svg';
            $Question->save();
            $request->request->add(['question_id' => $Question->id]);
            foreach (json_decode($request->option_answer_ids) as $k => $v) {
                $OptionAnswer = new OptionAnswer();
                $request->request->set('name', $v->value);
                $OptionAnswer->fill($request->all())->save();
                if ($v->checked) $Question->where('question.id', $Question->id)->update(['question.option_answer_id' => $OptionAnswer->id]);
            }
            DB::commit();
            return response()->json($request_all, 200);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json($e->getMessage(), 412);
        }
    }

    function loadExam(Request $request)
    {
        try {
            $data = $this->prepareExam($request->theme_id, ['question.id', 'question.theme_id', 'question.name as question_name', 'question.image as question_image', 'theme.name as theme_name'], true);
            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 412);
        }
    }

    private function prepareExam($theme_id, $colums, $flag = false)
    {
        $data = $this->allQuestionByTheme($theme_id, $colums);
        //Ciclo para recorrer los datos extraidos de la Base de Datos.
        foreach ($data as $k => $v) {
            $data[$k]['options_answers'] = (new OptionAnswerController)->allOptionAnswerByQuestion($v['id'], $flag);
        }
        //Alear las posiciones del arreglo asociativo.
        if ($flag) shuffle($data);

        return $data;
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
                return response()->json($request->all(), 200);
            } else {
                throw new Exception('Error, no se ha recibido el arreglo de respuestas.');
            }
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 412);
        }
    }

    function loadExamSolution(Request $request)
    {
        try {
            $dataExamSolution = UserSurveyTheme::where('id', $request->user_survey_theme_id)->first();
            $dataExam = $this->prepareExam($dataExamSolution->theme_id, ['question.id', 'question.theme_id', 'question.name as question_name', 'question.image as question_image', 'question.option_answer_id', 'theme.name as theme_name']);
            $data = ['dataExam' => []];
            foreach ($dataExam as $k => $v) {
                foreach (json_decode($dataExamSolution->option_answer_ids) as $kk => $vv) {
                    if ($v['id'] == $vv->question_id) {
                        array_push($data['dataExam'], $v);
                        $data['dataExam'][$k]['user_option_answer_id'] = $vv->option_answer_ids;
                    }
                }
            }
            $data['score'] = $dataExamSolution->score;
            return response()->json(['dataExamSolution' => $data, 'load' => true], 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 412);
        }
    }

    private function allQuestionByTheme($theme_id, $colums)
    {
        return Question::select($colums)
            ->join('theme', 'theme.id', 'question.theme_id')
            ->where('question.theme_id', $theme_id)
            ->where('question.status', 'A')
            ->get()->toArray();
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
        $puntaje = round($correctas * $puntaje_por_pregunta);//devolver un redondeo con precision
        $this->updateUserSurveyTheme($request, $puntaje, $option_answer_ids);
    }

    private function updateUserSurveyTheme($request, $puntaje, $option_answer_ids)
    {
        UserSurveyTheme::where('id', $request->user_survey_theme_id)->update(['option_answer_ids' => json_encode($option_answer_ids), 'score' => $puntaje, 'status' => 'PR']);
    }

    //Verificar la programacion para poder visualizar la solucion del examen
    function verifyExamSolution(Request $request)
    {
        if (DB::select('CALL SP_VERIFY_EXAM_SOLUTION(?)', [$request->user_survey_theme_id])[0]->value) {
            return response()->json(true, 200);
        } else {
            return response()->json('Este exámen se encuentra en ejecución, vuelva intentarlo despues de la fecha programada.', 412);
        }
    }

}