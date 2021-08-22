<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

class RequestId
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

        $uuid = $request->headers->get('X-Request-ID');
        if (is_null($uuid)) {
            $uuid = Uuid::uuid4()->toString();
            $request->headers->set('X-Request-ID', $uuid);
        }

        $response->headers->set('X-Request-ID', $uuid);

        return $response;
    }
}
