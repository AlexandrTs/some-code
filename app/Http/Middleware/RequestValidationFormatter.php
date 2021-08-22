<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RequestValidationFormatter
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
        $response = $next($request);

        if($response->getStatusCode() == 422){
            $content = json_decode($response->content(), true);
            if($content && isset($content['message'], $content['errors'])){
                $out = [
                    'status' => 'error',
                    'message' => 'Invalid request payload',
                    'detail' => $content['errors'],
                ];
                $response->setContent(json_encode($out, JSON_UNESCAPED_UNICODE));
            }
        }

        return $response;
    }
}
