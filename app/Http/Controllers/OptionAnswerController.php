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
        $query = new OptionAnswer();
        $query = $query->select(["option_answer.*"])
            ->join("question","question.id","option_answer.question_id")
            ->leftJoin("theme","theme.id","question.theme_id");
        if($request->get("theme_id") != "") $query = $query->where("theme.id",$request->theme_id);
        if($request->get("question_id") != "") $query = $query->where("option_answer.question_id",$request->question_id);
        if($request->get("status") != "") $query = $query->where("option_answer.status",$request->status);
//        var_dump($query->toSql());
        return $query->get()->toArray();
    }

    function allOptionAnswerByQuestion($question_id)
    {
        return (new OptionAnswer())
            ->select("option_answer.*")
            ->join("question", "question.id", "option_answer.question_id")
            ->where("option_answer.question_id", $question_id)
            ->where("option_answer.status", "A")
            ->get()
            ->toArray();
    }

    function create(Request $request)
    {
        $OptionAnswer = new OptionAnswer();
        $this->validate($request,$OptionAnswer->returnRules($request));
        DB::beginTransaction();
        try {
            $OptionAnswer->fill($request->all())->save();
            return DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return DB::statement("ALTER TABLE " . $OptionAnswer->getTable() . " AUTO_INCREMENT = " . (count($OptionAnswer->all()) + 1));
        }
    }

    function update(Request $request){
        $OptionAnswer = new OptionAnswer();
        $this->validate($request,$OptionAnswer->returnRules($request));
        DB::beginTransaction();
        try{
            $OptionAnswer->where("id",$request->option_answer_id)->update(["question_id"=>$request->question_id]);
       return DB::commit();
        }catch (Exception $e){
            DB::rollback();
        return DB::statement("ALTER TABLE " . $OptionAnswer->getTable() . " AUTO_INCREMENT = " . (count($OptionAnswer->all()) + 1));
        }
    }
}
