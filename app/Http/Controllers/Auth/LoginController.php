<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
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

    public function searchUser($request)
    {
            $data = User::select([
                'users.id',
                'users.role_id AS role',
                'users.project_id AS project',
                'users.name',
                'users.username',
                'users.email',
                'users.status',
                'role.name AS role_name',
                'role.status AS role_status',
                'project.name AS project_name',
                'project.status AS project_status'
            ])
                ->join('role', 'role.id', '=', 'users.role_id')
                ->join('project', 'project.id', '=', 'users.project_id')
                ->where('users.username', $request->username)
                ->first();

                $data->role = ['id' => $data->role, 'name' => $data->role_name, 'status' => $data->role_status];
                $data->project = ['id' => $data->project, 'name' => $data->project_name, 'status' => $data->project_status];
                return $data;
    }

    function validateIfExist(Request $request)
    {
        try {
            $User = User::where('username', $request->username)->first();
            if ($User) {
                $data = $this->searchUser($request);
                return response()->json($data, 200);
            } else {
                $this->createUser($request);
                $data = $this->searchUser($request);
                return response()->json($data, 201);
            }
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 412);
        }
    }

    private function createUser($request)
    {
        $User = new User();
        $User->fill($request->all());
        $User->email = $request->username.'@sapia.com.pe';//siempre es invitado
        $User->role_id = 5;//inicializar como invitado
        $User->project_id = 1;//inicializar con ningun proyecto asignado
        $User->status = 'A';//inicializar como activo
        return $User->save();
    }

    function getConfig(Request $request)
    {
        $User = User::where('',$request->username)->first();
        return response()->json($User,200);
    }

}
