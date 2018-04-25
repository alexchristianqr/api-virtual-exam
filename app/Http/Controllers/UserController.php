<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Http\Request;

class UserController extends LoginController
{
    function getConfig(Request $request)
    {
        $data = $this->searchUser($request);
        return response()->json($data,200);
    }
}
