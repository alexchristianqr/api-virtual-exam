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
      foreach ($dataUsers as $k => $v) {
        array_push($data, ['id' => $v['id'], 'value' => $v['name']]);
      }
      return response()->json($dataUsers, 200);
    } catch (Exception $e) {
      return response()->json($e->getMessage());
    }
  }

  function all(Request $request)
  {
    try {
      if ($request->get('status') != '') {
        $dataUsers = User::where('status', $request->status)->get();
      } else {
        $dataUsers = User::get();
      }
      return response()->json($dataUsers, 200);
    } catch (Exception $e) {
      return response()->json($e->getMessage(), 412);
    }
  }

}