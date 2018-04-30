<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\LoginController;
use App\User;
use Illuminate\Http\Request;
use Exception;

class UserController extends LoginController
{
    function getConfig(Request $request)
    {
        try {
            $data = $this->searchUser($request);
            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 412);
        }
    }

    function getUsers()
    {
        try {
            $data = [];
            $dataUsers = User::where('status', 'A')->get(['id', 'name'])->toArray();
            array_unshift($dataUsers, ['id' => 0, 'name' => 'Todos']);
            foreach ($dataUsers as $k => $v) {
                if ($k == 0) {
                    array_push($data, ['id' => 0, 'value' => 'Todos']);
                } else {
                    array_push($data, ['id' => $v['id'], 'value' => $v['name']]);
                }
            }
            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage());
        }
    }
}
