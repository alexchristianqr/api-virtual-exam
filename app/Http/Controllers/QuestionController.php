<?php

namespace App\Http\Controllers;

use App\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class QuestionController extends Controller
{

    function all(Request $request)
    {
        try {
            $Question = Question::select();
            if ($request->get('theme_id') != "") $Question = $Question->where('question.theme_id', $request->theme_id);
            if ($request->get('level') != "") $Question = $Question->where('question.level', $request->level);
            if ($request->get('status') != "") $Question = $Question->where('question.status', $request->status);
            return response()->json($Question->get(), 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 412);
        }
    }

    function create(Request $request)
    {
        $question = new Question();
        $this->validate($request, $question->returnRules($request));
        DB::beginTransaction();
        try {
            $question->fill($request->all())->save();
            return DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return DB::statement('ALTER TABLE ' . $question->getTable() . ' AUTO_INCREMENT = ' . (count($question->all()) + 1) . ';');
        }
    }

    function update($question_id, Request $request)
    {
        $question = new Question();
        $this->validate($request, $question->returnRules($request));
        DB::beginTransaction();
        try {
            $question->where('question.id', $question_id)->update($request->all());
            return DB::commit();
        } catch (Exception $e) {
            echo $e->getMessage();
            DB::rollBack();
            return DB::statement('ALTER TABLE ' . $question->getTable() . ' AUTO_INCREMENT = ' . (count($question->all()) + 1) . ';');
        }
    }

}