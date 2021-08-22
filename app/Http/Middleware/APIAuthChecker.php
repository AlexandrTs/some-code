<?php

namespace App\Http\Middleware;

use App\Enums\EnvEnum;
use App\Services\SignService;
use Closure;
use Illuminate\Http\Request;

class APIAuthChecker
{
    private $no_auth_routes = [
        // ...
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $authorized = false;

        // healthcheck route skip auth
        if(in_array($request->getPathInfo(), $this->no_auth_routes)){
            return $next($request);
        }

        //@TODO если нужны запросы через постман, сделать JWT токен или Digest в два шага (базовая только для теста и дева)
        $basic_auth_disabled_envs = [
           EnvEnum::ENV_PRODUCTION,
           EnvEnum::ENV_STAGING,
        ];
        if(!in_array(config('app.env'), $basic_auth_disabled_envs)){
           $authorization = $request->header('Authorization');
           if ($authorization) {
               $parts = explode(' ', $authorization);
               if (isset($parts[1])) {
                   $username = config('api.jysan_payment_client_username');
                   $password = config('api.jysan_payment_client_password');
                   if (!$username || !$password) {
                       return response(['status' => 'error', 'message' => 'Basic authorization is not configured'], 400);
                   }
                   if (base64_encode($username . ':' . $password) == $parts[1]) {
                       $authorized = true;
                   } else {
                       return response(['status' => 'error', 'message' => 'Invalid credentials'], 401);
                   }
               }
           }
        }

        // авторизация по подписи
        if(!$authorized) {
           $query_params = $request->request->all();
           $shared_key = config('api.jysan_payment_client_shared_key');
           if(!$shared_key){
               return response(['status' => 'error', 'message' => 'Required configuration missing'], 400);
           }
           $sign = new SignService($shared_key);
           if (!$sign->checkSign($query_params)) {
               return response(['status' => 'error', 'message' => $sign->getLastError()], 401);
           }
        }

        return $next($request);
    }
}
