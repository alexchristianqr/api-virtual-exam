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
        try {
            $Project = Project::where('project.status', 'A')->get();
            return response()->json($Project, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 412);
        }
    }

    function update($user_id, Request $request)
    {
        try {
            User::where('id', $user_id)->update(['project_id' => $request->project_id]);
            return response()->json($request->all(), 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 412);
        }
    }
}
