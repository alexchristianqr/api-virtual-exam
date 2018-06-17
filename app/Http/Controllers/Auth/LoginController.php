<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Services\UserService;
use App\User;
use function GuzzleHttp\Psr7\str;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Exception;

class LoginController extends Controller
{
  /*
  |--------------------------------------------------------------------------
  | Login Controller
  |--------------------------------------------------------------------------
  |
  | This controller handles authenticating users for the application and
  | redirecting them to your home screen. The controller uses a trait
  | to conveniently provide its functionality to your applications.
  |
  */

  use AuthenticatesUsers;

  /**
   * Where to redirect users after login.
   *
   * @var string
   */
  protected $redirectTo = '/home';

  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {
    $this->middleware('cors');
  }

  public function login(Request $request)
  {
    try {
      $User = (new UserService())->searchUser($request);
      if ($User) {
        return response()->json($User, 200);
      } else {
        return response()->json("User not found", 412);
      }
    } catch (Exception $e) {
      return response()->json($e->getMessage(), 412);
    }
  }

  function validateIfExist(Request $request)
  {
    $status = 412;
    try {
      $dataUser = User::where('username', $request->username)->first();
      if ($dataUser) {
        switch ($dataUser->status) {
          case 'I':
            $status = 401;
            throw new Exception(utf8_encode( '<span>Estimado usuario su cuenta esta <b>Inactiva</b>, por favor cont&aacutecte al administrador del sistema.<span>'), $status);
            break;
          case 'E':
            $status = 401;
            throw new Exception(utf8_encode('<span>Estimado usuario su cuenta esta <b>Eliminada</b>, por favor cont&aacutecte al administrador del sistema.<span>'), $status);
            break;
          default:
            $User = (new UserService())->searchUser($request);
            break;
        }
        return response()->json($User, 200);
      } else {
        (new UserService())->createUser($request);
        $User = (new UserService())->searchUser($request);
        return response()->json($User, 201);
      }
    } catch (Exception $e) {
      return response()->json($e->getMessage(), $status);
    }
  }


}
