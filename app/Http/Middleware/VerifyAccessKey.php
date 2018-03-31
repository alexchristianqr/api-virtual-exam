<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Illuminate\Http\Request;

class VerifyAccessKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Obtenemos el api-key que el usuario envia
        $appkey = $request->header('X-CSRF-Auth-Token');
        // Si coincide con el valor almacenado en la aplicacion
        // la aplicacion se sigue ejecutando
//        User::where('email', $request->input('email'))->update(['api_key' => "$appkey"]);
//        if ($appkey === env('APP_KEY')) {
//        var_dump($request->session());
//        env("APP_KEY_AUTH","098038fgfdg0840939483290479023");
//        var_dump($request->session()->get("myappkey"));
//        dd(env("APP_KEY_AUTH"));
//        session(["myapikey"=>$apikey]);
//        var_dump(auth()->user()->getAuthIdentifier());
//        env('APP_KEY_AUTH',session("myapikey"));
        if ($appkey === env('APP_KEY_AUTH')) {
            return $next($request);
        } else {
            // Si falla devolvemos el mensaje de error
            return response()->json(['error' => 'unauthorizedttttt'.env('APP_KEY_AUTH')], 401);
        }
    }
}
