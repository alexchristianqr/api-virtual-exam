<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
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
//        $this->middleware('guest')->except('logout');
    }

    /**
     * API Register
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $credentials = $request->only('name', 'email', 'password');

        $rules = [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users'
        ];
        $validator = Validator::make($credentials, $rules);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages()]);
        }
        $name = $request->name;
        $email = $request->email;
        $password = $request->password;

        $user = User::create(['name' => $name, 'email' => $email, 'password' => Hash::make($password)]);
        $verification_code = str_random(30); //Generate verification code
        DB::table('user_verifications')->insert(['user_id' => $user->id, 'token' => $verification_code]);
        return response()->json(['success' => true, 'message' => 'Thanks for signing up! Please check your email to complete your registration.']);
    }

    /**
     * API Verify User
     *
     * @param $verification_code
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyUser($verification_code)
    {
        $check = DB::table('user_verifications')->where('token', $verification_code)->first();
        if (!is_null($check)) {
            $user = User::find($check->user_id);
            if ($user->is_verified == 1) {
                return response()->json([
                    'success' => true,
                    'message' => 'Account already verified..'
                ]);
            }
            $user->update(['is_verified' => 1]);
            DB::table('user_verifications')->where('token', $verification_code)->delete();
            return response()->json([
                'success' => true,
                'message' => 'You have successfully verified your email address.'
            ]);
        }
        return response()->json(['success' => false, 'error' => "Verification code is invalid."]);
    }


    /**
     * API Login, on success return JWT Auth token
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    /**
     * Log out
     * Invalidate the token, so user cannot use it anymore
     * They have to relogin to get a new token
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

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

}
