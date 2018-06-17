<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Services\UserService;
use App\User;
use Illuminate\Http\Request;
use Exception;

class UserController extends LoginController
{

  function getConfig(Request $request)
  {
    try {
      $data = (new UserService())->searchUser($request);
      return response()->json($data, 200);
    } catch (Exception $e) {
      return response()->json($e->getMessage(), 412);
    }
  }

  function getUsers(Request $request)
  {
    try {
      $data = [];
      $dataUsers = (new UserService())->getUsers($request);
      foreach ($dataUsers as $k => $v) {
        array_push($data, ['id' => $v['id'], 'value' => $v['name']]);
      }
      return response()->json($data, 200);
    } catch (Exception $e) {
      return response()->json($e->getMessage(), 412);
    }
  }

  function all(Request $request)
  {
    try {
      $dataUsers = (new UserService())->all($request);
      return response()->json($dataUsers, 200);
    } catch (Exception $e) {
      return response()->json($e->getMessage(), 412);
    }
  }

}