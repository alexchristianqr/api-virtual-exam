<?php

namespace App\Http\Controllers;

use App\OptionAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class OptionAnswerController extends Controller
{

  function all(Request $request)
  {
    try {
      $OptionAnswer = OptionAnswer::select(["option_answer.*"])
        ->join("question", "question.id", "option_answer.question_id")
        ->leftJoin("theme", "theme.id", "question.theme_id");
      if ($request->get("theme_id") != "") $OptionAnswer = $OptionAnswer->where("theme.id", $request->theme_id);
      if ($request->get("question_id") != "") $OptionAnswer = $OptionAnswer->where("option_answer.question_id", $request->question_id);
      if ($request->get("status") != "") $OptionAnswer = $OptionAnswer->where("option_answer.status", $request->status);
      return response()->json($OptionAnswer->get(), 200);
    } catch (Exception $e) {
      return response()->json($e->getMessage(), 412);
    }
  }

  function create(Request $request)
  {
    $NewOptionAnswer = new OptionAnswer();
    DB::beginTransaction();
    try {
      $NewOptionAnswer->fill($request->all())->save();
      return DB::commit();
    } catch (Exception $e) {
      DB::rollBack();
      return DB::statement("ALTER TABLE " . $NewOptionAnswer->getTable() . " AUTO_INCREMENT = " . (count($NewOptionAnswer->all()) + 1));
    }
  }

  function update(Request $request)
  {
    $NewOptionAnswer = new OptionAnswer();
    $this->validate($request, $NewOptionAnswer->returnRules($request));
    DB::beginTransaction();
    try {
      $NewOptionAnswer->where("id", $request->option_answer_id)->update(["question_id" => $request->question_id]);
      return DB::commit();
    } catch (Exception $e) {
      DB::rollback();
      return DB::statement("ALTER TABLE " . $NewOptionAnswer->getTable() . " AUTO_INCREMENT = " . (count($NewOptionAnswer->all()) + 1));
    }
  }

  function allOptionAnswerByQuestion($question_id, $flag)
  {
    $NewOptionAnswer = OptionAnswer::select("option_answer.*")
      ->join("question", "question.id", "option_answer.question_id")
      ->where("option_answer.question_id", $question_id)
      ->where("option_answer.status", "A")
      ->get()
      ->toArray();
    if ($flag) shuffle($NewOptionAnswer);
    return $NewOptionAnswer;
  }

}
