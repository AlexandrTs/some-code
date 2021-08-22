<?php

namespace App\Http\Middleware;

use App\Contracts\APILoggerInterface;
use App\Services\RequestResponseAPILoggerService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Route;

class LogIncomingAPIRequest
{
    private $logger;

    public function __construct(RequestResponseAPILoggerService $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @var string[] controller with actions which need to log only on errors
     */
    private $log_errors_only = [
        'App\Http\Controllers\Healthcheck@readiness',
        'App\Http\Controllers\Healthcheck@liveness',
        'App\Http\Controllers\Healthcheck@healthcheck',
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
        $response = $next($request);

        try {
            $controller = $request->route()->action['controller'];
            $log_request = true;
            if(in_array($controller, $this->log_errors_only)){
                if($response->status() > 200){
                    $log_request = true;
                }else {
                    $log_request = false;
                }
            }
            if($log_request){
                $data = [
                    'method' => $request->getMethod(),
                    'url' => $request->getUri(),
                    'params' => $request->all(),
                    'status_code' => $response->status(),
                    'response' => json_encode(
                        json_decode($response->content(), true),
                        JSON_UNESCAPED_UNICODE
                    ),
                    'incoming' => 1,
                ];

                $this->logger->log($data, $controller);
            }
        }catch(\Exception $e){
            // silently ignore
        }

        return $response;

    }
}
