<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Throwable;
use Illuminate\Auth\Access\AuthorizationException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $exception)
    {

        if($exception instanceof AuthorizationException){
            return response([
                'status' => 'error',
                'message' => 'Authorization required',
                'details' => $exception->getMessage()
            ], 403);
        }

        if ($exception instanceof MethodNotAllowedHttpException) {
            return response(['status' => 'error', 'message' => 'Method not allowed'], 405);
        }

        if ($exception instanceof \Illuminate\Database\QueryException) {
            Log::error('Database query exception', [
                'message' => $exception->getMessage(),
                'trace' => $exception->getTrace(),
                'host' => config('database.connections.cscart.host'),
                'database' => config('database.connections.cscart.database'),
                'username' => config('database.connections.cscart.username'),
            ]);
            return response(['status' => 'error', 'message' => 'Database query exception'], 500);
        }

        if ($exception instanceof \PDOException) {
            Log::error('Database connection error', ['message' => $exception->getMessage(), 'trace' => $exception->getTrace()]);
            return response(['status' => 'error', 'message' => 'Database connection error'], 500);
        }

        return parent::render($request, $exception);
    }

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {

        });
    }
}
