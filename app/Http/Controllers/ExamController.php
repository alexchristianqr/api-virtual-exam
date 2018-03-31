<?php

namespace App\Http\Controllers;

use App\Question;
use Illuminate\Http\Request;

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
        $data = $this->allQuestionByTheme($request->theme_id, ["question.id", "question.theme_id", "question.name as question_name", "theme.name as theme_name"]);
        //Ciclo para recorrer los datos extraidos de la Base de Datos.
        foreach ($data as $k => $v) {
            $data[$k]["options_answers"] = (new OptionAnswerController)->allOptionAnswerByQuestion($v['id']);
        }
        //Reordenar las posiciones del arreglo asociativo.
        shuffle($data);
        return $data;
    }


}