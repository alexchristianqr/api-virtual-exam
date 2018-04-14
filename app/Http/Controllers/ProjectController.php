<?php

namespace App\Http\Controllers;

use App\Project;
use App\User;
use Illuminate\Http\Request;
use Exception;

class ProjectController extends Controller
{
    function all()
    {
        return response()->json(Project::get(),200);
    }

    function update($user_id,Request $request)
    {
        try{
            return User::where('id',$user_id)->update(['project_id'=>$request->project_id]);
        }catch(Exception $e){
            return response()->json($e->getMessage(),412);
        }
    }
}
